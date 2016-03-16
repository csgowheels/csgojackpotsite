﻿using System;
using System.Linq;
using System.Security.AccessControl;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using SteamKit2;
using SteamTrade;
using SteamTrade.TradeOffer;
using SteamTrade.TradeWebAPI;
using MySql.Data.MySqlClient;
using System.Collections.Generic;  //Its for MySQL.

namespace SteamBot
{
    class CommisionBotUserHandler : UserHandler
    {
        private System.Timers.Timer referalCheck;
        private System.Timers.Timer getOfferStateTimer;

        #region SimpleUserHandler Overrides

        public TF2Value AmountAdded;

        public CommisionBotUserHandler (Bot bot, SteamID sid) : base(bot, sid) {}

        public override bool OnGroupAdd()
        {
            return false;
        }

        public override bool OnFriendAdd () 
        {
            return false;
        }

        public override void OnChatRoomMessage(SteamID chatID, SteamID sender, string message)
        {
            Log.Info(Bot.SteamFriends.GetFriendPersonaName(sender) + ": " + message);
            base.OnChatRoomMessage(chatID, sender, message);
        }

        public override void OnFriendRemove () {}
        
        public override void OnMessage (string message, EChatEntryType type) 
        {
            SendChatMessage(Bot.ChatResponse);
        }

        public override bool OnTradeRequest() 
        {
            return true;
        }
        
        public override void OnTradeError (string error) 
        {
            SendChatMessage("Oh, there was an error: {0}.", error);
            Log.Warn (error);
        }
        
        public override void OnTradeTimeout () 
        {
            SendChatMessage("Sorry, but you were AFK and the trade was canceled.");
            Log.Info ("User was kicked because he was AFK.");
        }
        
        public override void OnTradeInit() 
        {
            SendTradeMessage("Success. Please put up your items.");
        }
        
        public override void OnTradeAddItem (Schema.Item schemaItem, Inventory.Item inventoryItem) {}
        
        public override void OnTradeRemoveItem (Schema.Item schemaItem, Inventory.Item inventoryItem) {}
        
        public override void OnTradeMessage (string message) {}
        
        public override void OnTradeReady (bool ready) 
        {
            if (!ready)
            {
                Trade.SetReady (false);
            }
            else
            {
                if(Validate ())
                {
                    Trade.SetReady (true);
                }
                SendTradeMessage("Scrap: {0}", AmountAdded.ScrapTotal);
            }
        }

        public override void OnTradeSuccess()
        {
             Log.Success("Trade Complete.");
        }

        public override void OnTradeAwaitingConfirmation(long tradeOfferID)
        {
            Log.Warn("Trade ended awaiting confirmation");
            SendChatMessage("Please complete the confirmation to finish the trade");
        }

        public override void OnTradeAccept() 
        {
            if (Validate() || IsAdmin)
            {
                //Even if it is successful, AcceptTrade can fail on
                //trades with a lot of items so we use a try-catch
                try {
                    if (Trade.AcceptTrade())
                        Log.Success("Trade Accepted!");
                }
                catch {
                    Log.Warn ("The trade might have failed, but we can't be sure.");
                }
            }
        }

        public bool Validate ()
        {            
            AmountAdded = TF2Value.Zero;
            
            List<string> errors = new List<string> ();
            
            foreach (TradeUserAssets asset in Trade.OtherOfferedItems)
            {
                var item = Trade.OtherInventory.GetItem(asset.assetid);
                if (item.Defindex == 5000)
                    AmountAdded += TF2Value.Scrap;
                else if (item.Defindex == 5001)
                    AmountAdded += TF2Value.Reclaimed;
                else if (item.Defindex == 5002)
                    AmountAdded += TF2Value.Refined;
                else
                {
                    var schemaItem = Trade.CurrentSchema.GetItem (item.Defindex);
                    errors.Add ("Item " + schemaItem.Name + " is not a metal.");
                }
            }
            
            if (AmountAdded == TF2Value.Zero)
            {
                errors.Add ("You must put up at least 1 scrap.");
            }
            
            // send the errors
            if (errors.Count != 0)
                SendTradeMessage("There were errors in your trade: ");
            foreach (string error in errors)
            {
                SendTradeMessage(error);
            }
            
            return errors.Count == 0;
        }
        #endregion

        public override void OnLoginCompleted()
        {
            if (referalCheck == null || !referalCheck.Enabled)
            {
                referalCheck = new System.Timers.Timer();
                referalCheck.Elapsed += new System.Timers.ElapsedEventHandler(OnTimed);
                referalCheck.Interval = 15000;
                referalCheck.AutoReset = false;
                referalCheck.Enabled = true;
                referalCheck.Start();
            }

            // timer for checking accepted offers
            if (getOfferStateTimer == null || !getOfferStateTimer.Enabled)
            {
                getOfferStateTimer = new System.Timers.Timer();
                getOfferStateTimer.Elapsed += new System.Timers.ElapsedEventHandler(getOfferState);
                getOfferStateTimer.Interval = 15000;
                getOfferStateTimer.AutoReset = false;
                getOfferStateTimer.Enabled = true;
                getOfferStateTimer.Start();
            }
            updateCommisionDatabase();
        }

        /// <summary>
        /// Method will be called on each TradeOffer
        /// Checks offer, if items are correct, and partner sending is bot4 or bot 5 it accepts it
        /// otherwise nothing.
        /// </summary>
        /// <param name="offer"></param>
        public override void OnNewTradeOffer(TradeOffer offer)
        {
            try
            {
                List<SteamTrade.TradeOffer.TradeOffer.TradeStatusUser.TradeAsset> myItemList = offer.Items.GetMyItems();
                List<SteamTrade.TradeOffer.TradeOffer.TradeStatusUser.TradeAsset> theirItemList = offer.Items.GetTheirItems();

                // check if all items are csgo items
                bool csgoItems = true;
                for (int i = 0; i < theirItemList.Count; i++)
                    if (!(theirItemList[i].AppId == 730))
                        csgoItems = false;

                // check from who is offer comming and if item count (and type) is allright
                if (offer.PartnerSteamId.ConvertToUInt64() == (ulong)botId4 || offer.PartnerSteamId.ConvertToUInt64() == (ulong)botId5)
                    if (myItemList.Count == 0 && theirItemList.Count > 0 && csgoItems)
                        if (offer.Accept())
                        {
                            //updateCommisionDatabase();
                        }
            }
            catch (Exception ex)
            {
                Console.Write(ex.Message);
            }
        }

        /// <summary>
        /// Function designed to send offers from commision bot to users (who refered other users to our site).
        /// It reads database (getReferalOffers()) and creates all offers and sends them.
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        public void OnTimed(object sender, System.EventArgs e)
        {
            // stop timer
            referalCheck.Stop();

            // read database to find offers which should be created
            System.Data.DataSet ds = getReferalOffers();

            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                referalCheck.Start();
                return;
            }

            try
            {
                List<Int64> asset_id = new List<Int64>();
                int total_items = 0, security_code = 0, new_offer = 0;
                Int64 steamid = 0;

                foreach (System.Data.DataRow row in ds.Tables[0].Rows)
                {
                    if (Convert.ToInt16(row["isNew"]) == 1) // start of trade offer -> (execute previous offer)
                    {
                        if (new_offer == 1)
                        {
                            sendOfferToUser(steamid, asset_id.ToArray(), security_code);
                            asset_id.Clear();
                        }

                        new_offer = 1;
                        security_code = Convert.ToInt32(row["security_code"]);
                        steamid = Convert.ToInt64(row["steamid"]);
                        asset_id.Add(Convert.ToInt64(row["assetid"]));
                        total_items++;
                    }
                    else // isnew = 0 (one item in trade offer)
                    {
                        asset_id.Add(Convert.ToInt64(row["assetid"]));
                        security_code = Convert.ToInt32(row["security_code"]);
                        total_items++;
                    }
                }

                // execute last trade
                sendOfferToUser(steamid, asset_id.ToArray(), security_code);
                asset_id.Clear();
                referalCheck.Start();
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
                referalCheck.Start();
            }
        }

        #region OnTimed helper function
        /// <summary>
        /// Function designed to handle database connection, all it does
        /// it returns data from new_offer table where is_checked variable is set to 0
        /// (which means that those offers should be checked (sent by bots to user requested them)
        /// </summary>
        /// <returns></returns>
        private System.Data.DataSet getReferalOffers()
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM new_reward WHERE isChecked=0";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                return ds;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
                Console.WriteLine("Failed in database selection");
            }

            conn.Close();
            return ds;
        }

        /// <summary>
        /// This function sent trade offer to user (tries to send trade offer), depending on parameters
        /// sent to it. Uses list of items (int64) to create new offer, and security code to call function
        /// setOfferChecked(), which updates database.
        /// </summary>
        /// <param name="steamid"></param>
        /// <param name="tot_items"></param>
        /// <param name="security_code"></param>
        private void sendOfferToUser(Int64 steamid, Int64[] tot_items, int security_code)
        {
            SteamID othersSteamId = new SteamID((ulong)steamid);

            var offers2 = Bot.NewTradeOffer(othersSteamId);

            for (int i = 0; i < tot_items.Count(); i++)
                if (!offers2.Items.AddMyItem(730, 2, tot_items[i]))
                {
                    Log.Error("Commision Bot: Failed adding item to offer. (user: {0}; assetID: {1})", steamid, tot_items[i]);
                    deleteOfferFromDataBase(steamid, security_code);
                }

            if (offers2.Items.NewVersion)
            {
                string token = getUserToken(othersSteamId);
                if (token != "")
                {
                    string newsOfferId;
                    try
                    {
                        var escrow_duration = Bot.GetEscrowDuration(othersSteamId, token);

                        if (escrow_duration.DaysTheirEscrow == 0)  // checks for escrow, if days in escrow are null, send the offer, else show error and delete offer from database.
                        {
                            if (offers2.SendWithToken(out newsOfferId, token, "Security code is: " + Convert.ToString(security_code)))
                            {
                                Bot.AcceptAllMobileTradeConfirmations();
                                Log.Success("Trade offer sent. Offer ID: \"{0}\" SteamId: \"{1}\"", newsOfferId, steamid);
                                setOfferChecked(steamid, newsOfferId, offers2.PartnerSteamId.AccountID, security_code);

                                deleteItemsFromCommision(tot_items); 
                                // delete points (charge price)
                                updateUserPoints(newsOfferId);
                            }
                            else
                            {
                                Log.Error("Recieve Bot [#{0}]: Failed sending offer to user: {1}", Bot.DisplayName == botName2 ? 2 : 3, steamid);
                                deleteOfferFromDataBase(steamid, security_code);
                            }
                        }
                        else
                        {
                            Log.Error("User: {0} is in  escrow, offer will be deleted ", steamid);
                            deleteOfferFromDataBase(steamid, security_code);
                        }
                    }
                    catch (Exception)
                    {
                        Log.Error("Recieve Bot [#{0}]: Failed sending offer to user: {1}", Bot.DisplayName == botName2 ? 2 : 3, steamid);
                        deleteOfferFromDataBase(steamid, security_code);
                    }
                }
            }
        }

        /// <summary>
        /// It updates new_reward table, setting isChecked on 1, offerId on appropriate offerID, botId on executing bot's
        /// steamid, accountId on appropriate accountid, and finds all rows depending on security code (which is "unique" for each trade)
        /// and steamID (security question).
        /// </summary>
        /// <param name="steamid"></param>
        /// <param name="newsOfferId"></param>
        /// <param name="accountId"></param>
        /// <param name="security_code"></param>
        private void setOfferChecked(long steamid, string newsOfferId, uint accountId, int security_code)
        {
            try
            {
                using (MySqlConnection cn = new MySqlConnection(MyConnection2))
                {
                    MySqlCommand cmd = new MySqlCommand();
                    cmd.Connection = cn;
                    cmd.CommandText = "UPDATE new_reward SET isChecked=1,offerid=" + newsOfferId + ", botid='" + botId1 + "'"
                                        + ", accountid='" + accountId + "' WHERE security_code=" + security_code + " AND steamid=" + steamid;
                    cn.Open();
                    int numRowsUpdated = cmd.ExecuteNonQuery();
                    cmd.Dispose();
                }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }
        }

        /// <summary>
        /// Method deletes all offers which offerids are invalid ('failed')
        /// </summary>
        private void deleteOfferFromDataBase(long steamid, int security_code)
        {
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
            try
            {
                string Query = "DELETE FROM new_reward WHERE security_code=" + security_code + " AND steamid=" + steamid;

                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
                MySqlDataReader MyReader2;
                MyConn2.Open();
                MyReader2 = MyCommand2.ExecuteReader();     // Here our query will be executed and data saved into the database.
                while (MyReader2.Read())
                { }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            MyConn2.Close();
        }
        #endregion

        /// <summary>
        /// Method connected with timer signal, task: check offers from database if any is accepted
        /// For each accepted trade offer it updates user points (subtract offer price), 
        /// deletes those offers from new_reward table.
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        protected virtual void getOfferState(object sender, System.EventArgs e)
        {
            // Console.WriteLine("Recieve Bot [#{0}]: in getOfferState", Bot.DisplayName == botName2 ? 2 : 3);

            // reset timer time
            getOfferStateTimer.Interval = 15000;
            getOfferStateTimer.Stop();

            System.Data.DataSet ds = getOfferDataBase();
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                getOfferStateTimer.Start();
                return;
            }

            bool isAnythingChanged = false;
            List<string> offers = new List<string>();

            for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                if (!offers.Contains(Convert.ToString(ds.Tables[0].Rows[i]["offerid"])))
                    offers.Add(Convert.ToString(ds.Tables[0].Rows[i]["offerid"]));

            foreach (string offerID in offers)
            {
                TradeOfferState tradeState = Bot.getTradeOfferState(offerID);
                if (tradeState == TradeOfferState.TradeOfferStateAccepted)
                {
                    setAccepted(offerID);
                    isAnythingChanged = true;
                    Int64[] asset_ids = getAssetIds(offerID);
                    deleteItemsFromDatabase(asset_ids, false); // clear temp table
                }
                else if (tradeState == TradeOfferState.TradeOfferStateDeclined
                        || tradeState == TradeOfferState.TradeOfferStateCanceled
                        || tradeState == TradeOfferState.TradeOfferStateExpired
                        || tradeState == TradeOfferState.TradeOfferStateInvalid
                        || tradeState == TradeOfferState.TradeOfferStateInvalidItems)
                {
                    // delete offer from database
                    setAccepted(offerID); // check it for delete
                    isAnythingChanged = true;

                    // return user's points
                    returnUserPoints(offerID);
                    Int64[] asset_ids = getAssetIds(offerID);
                    returnItemsToDatabase(asset_ids); // return items from temp table to commision table
                }
            }

            // If there was any offers worked through, try deleting all accepted offers (from new_offer)
            // and notifie server (php) that trade offers were accepted (jackpot maybe should start)
            if (isAnythingChanged)
            {
                isAnythingChanged = false;
                deleteOfferDataBase();
            }

            getOfferStateTimer.Start();
        }

        #region CheckOfferState function helepers

        /// <summary>
        /// returns all offers with given offerID (or all active if offerID not sent) which current bot sent
        /// todo: should be refactored to support bot 2 and bot 3 parallel working...
        /// </summary>
        /// <returns></returns>
        private System.Data.DataSet getOfferDataBase(String offerID = "")
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM new_reward WHERE isChecked=1 AND isaccepted=0" + (offerID == "" ? "" : " AND offerid=" + offerID);
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                return ds;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            conn.Close();
            return ds;
        }

        /// <summary>
        /// Updates new_offer table, setting isAccepted on 1 for offerID sent
        /// </summary>
        /// <param name="offerID"></param>
        private void setAccepted(string offerID)
        {
            try
            {
                using (MySqlConnection cn = new MySqlConnection(MyConnection2))
                {
                    MySqlCommand cmd = new MySqlCommand();
                    cmd.Connection = cn;
                    cmd.CommandText = "UPDATE new_reward SET isAccepted = 1 WHERE offerid='" + offerID + "'";
                    cn.Open();
                    int numRowsUpdated = cmd.ExecuteNonQuery();
                    cmd.Dispose();
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
        }

        /// <summary>
        /// Deletes all entries in database where isAccepted is 1
        /// </summary>
        private void deleteOfferDataBase()
        {
            cancelExpiredOffers();
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
            try
            {
                string Query = "delete from new_reward where isAccepted=1"; // or UNIX_TIMESTAMP() - time_created > 1800";

                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
                MySqlDataReader MyReader2;
                MyConn2.Open();
                MyReader2 = MyCommand2.ExecuteReader();     // Here our query will be executed and data saved into the database.
                while (MyReader2.Read())
                { }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }
            MyConn2.Close();
        }

        /// <summary>
        /// Gets offerids for each offer which is expired (past an hour), then tryies to find those offers, and
        /// then Cancels it.
        /// </summary>
        private void cancelExpiredOffers()
        {
            System.Data.DataSet ds = getExpiredOffers();
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
                return;

            foreach (System.Data.DataRow row in ds.Tables[0].Rows)
            {
                try
                {
                    TradeOffer offer;
                    if (Bot.TryGetTradeOffer(Convert.ToString(row["offerid"]), out offer))
                    {
                        if (offer != null)
                        {
                            if (offer.Cancel())
                            {
                                setAccepted(offer.TradeOfferId); // Convert.ToString(row["offerid"]);
                                Log.Info("Offer expired (Offer id: {0})", row["offerid"]);

                                // return user's points
                                returnUserPoints(offer.TradeOfferId);
                                Int64[] asset_ids = getAssetIds(offer.TradeOfferId);
                                returnItemsToDatabase(asset_ids); // return items from temp table to commision table
                            }
                        }
                    }
                }
                catch (Exception)
                {
                    Log.Error("Recieve Bot [#{0}]: Couldn't cancel offer with id: {1}", Bot.DisplayName == botName2 ? 2 : 3, row["offerid"]);
                }
            }
        }

        /// <summary>
        /// Function which return's list of assetids, it looks inside new_reward table, and subtract's
        /// all assetids for given offerid.
        /// </summary>
        /// <param name="offerId"></param>
        /// <returns></returns>
        private Int64[] getAssetIds(string offerId)
        {
            Int64[] asset_ids;
            System.Data.DataSet ds = new System.Data.DataSet();
            MySqlConnection conn = new MySqlConnection(MyConnection2);

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT assetid FROM new_reward WHERE offerid = " + offerId;
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            conn.Close();

            if (ds.Tables.Count != 0)
            {
                asset_ids = new Int64[ds.Tables[0].Rows.Count];
                for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                    asset_ids[i] = Convert.ToInt64(ds.Tables[0].Rows[i]["assetid"]);
            }
            else
                asset_ids = new Int64[0];

            return asset_ids;
        }

        /// <summary>
        /// Does mysql part of finding offerids for each offer which is expired
        /// </summary>
        /// <returns>DataSet filled with rows (offerid as key) for each offerid which is expired</returns>
        private System.Data.DataSet getExpiredOffers()
        {
            System.Data.DataSet ds = new System.Data.DataSet();
            MySqlConnection conn = new MySqlConnection(MyConnection2);

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT offerid FROM new_reward WHERE offerid <> 0 AND isAccepted = 0 AND isChecked = 1 AND UNIX_TIMESTAMP() - time_created > 1800 GROUP BY offerid";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            conn.Close();

            return ds;
        }
        #endregion

        #region User Point manipulation (database)
        /// <summary>
        /// Function which actually updates user points (subtract offer price).
        /// First it pulls steamid and offer price from new_reward table (looking by offerID),
        /// then pulls old user points from user table (looking by steamid) and then, acctually
        /// updates user table with new points for user.
        /// </summary>
        /// <param name="OfferID"></param>
        private void updateUserPoints(String OfferID)
        {
            System.Data.DataSet ds = getTotalSubtractPoints(OfferID);
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                Log.Error("No user found in table, points subtraction FAILED!!!");
                return;
            }
            // get points by subtracting cost of offer from old user points.
            int points = getOldUserPoints(Convert.ToString(ds.Tables[0].Rows[0]["steamid"])) - Convert.ToInt32(ds.Tables[0].Rows[0]["points"]);

            updateUserPointsMySql(Convert.ToString(ds.Tables[0].Rows[0]["steamid"]), points);
        }

        /// <summary>
        /// Function which actually updates user points (return user points for unsuccesuful trade).
        /// First it pulls steamid and offer price from new_reward table (looking by offerID),
        /// then pulls old user points from user table (looking by steamid) and then, acctually
        /// updates user table with new points for user.
        /// </summary>
        /// <param name="OfferID"></param>
        private void returnUserPoints(String OfferID)
        {
            System.Data.DataSet ds = getTotalSubtractPoints(OfferID);
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                Log.Error("No user found in table, points subtraction FAILED!!!");
                return;
            }
            // get points by subtracting cost of offer from old user points.
            int points = getOldUserPoints(Convert.ToString(ds.Tables[0].Rows[0]["steamid"])) + Convert.ToInt32(ds.Tables[0].Rows[0]["points"]);

            updateUserPointsMySql(Convert.ToString(ds.Tables[0].Rows[0]["steamid"]), points);
        }

        /// <summary>
        /// Helper function which returns steamid and total price of all items (looking by offerid)
        /// from new_reward table. ("steamid" => steamid, "points" => total cost of offer)
        /// </summary>
        /// <param name="OfferID"></param>
        /// <returns></returns>
        private System.Data.DataSet getTotalSubtractPoints(String OfferID)
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT steamid, sum(itprice) as points FROM new_reward WHERE offerid=" + OfferID;
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                return ds;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            conn.Close();
            return ds;
        }

        /// <summary>
        /// Pulls point information for user (from user table) and converts it into integer
        /// which is returned.
        /// </summary>
        /// <param name="steamID"></param>
        /// <returns></returns>
        private int getOldUserPoints(String steamID)
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();
            int retValue = 0;

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT points FROM user WHERE usteamid=" + steamID;
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
                {
                    Log.Error("No user found in table, points subtraction FAILED!!!");
                    return retValue;
                }
                retValue = Convert.ToInt32(ds.Tables[0].Rows[0]["points"]);

                conn.Close();
                return retValue;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }
            conn.Close();
            return retValue;
        }

        /// <summary>
        /// Function which updates user table, setting points to new value (send as parameter).
        /// </summary>
        /// <param name="steamID"></param>
        /// <param name="points"></param>
        private void updateUserPointsMySql(String steamID, int points)
        {
            try
            {
                using (MySqlConnection cn = new MySqlConnection(MyConnection2))
                {
                    MySqlCommand cmd = new MySqlCommand();
                    cmd.Connection = cn;
                    cmd.CommandText = "UPDATE user SET points = " + points + " WHERE usteamid='" + steamID + "'";
                    cn.Open();
                    int numRowsUpdated = cmd.ExecuteNonQuery();
                    cmd.Dispose();
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
        }
        #endregion

        #region Commision items (database)

        /// <summary>
        /// Function which loads implementation (load bot's items) and write all information into database,
        /// it does so, in order to release steam's site from overloaded requests for inventory (shop info).
        /// </summary>
        private void updateCommisionDatabase()
        {
            Console.WriteLine("In updateCommisionDatabase()");
            clearTable();

            IEnumerable<long> contextIds = new long[] { 2 };
            GenericInventory myInventory = new GenericInventory(Bot.SteamWeb);
            SteamID mySteamid = new SteamID(Convert.ToUInt64(botId1));

            myInventory.loadImplementation(730, contextIds, mySteamid);

            if (myInventory._items.Count == 0)
            {
                Console.WriteLine("Generic inventory empty.");
                return;
            }
            Console.WriteLine("Should be entered {0} items", myInventory._items.Count);

            try
            {
                using (MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2))
                {
                    //Console.WriteLine("In create offerItem ENtry");
                    MyConn2.Open();

                    string Query = "insert into commision_table(whoseitem, Amount, AppId, AssetId, ContextId, CurrecyId, ClassId, DescriptionId, itname, itimg) values ";

                    // put all items from one trade into one query (not one query per item)
                    for (int i = 0; i < myInventory._items.Count; i++)
                        Query += "(@whoseitem, @Amount, @AppId, @AssetId" + i + ", @ContextId, @CurrecyId, @ClassId" 
                                    + i + ", @DescriptionId" + i + ", @itname" +  i + ", @itimg" +  i + ")" + (i == myInventory._items.Count - 1 ? ";" : ",");

                    MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);

                    // static parameters (same for each item)
                    MyCommand2.Parameters.AddWithValue("whoseitem", "their"); // "their"
                    MyCommand2.Parameters.AddWithValue("Amount", 1); // 1
                    MyCommand2.Parameters.AddWithValue("AppId", 730);  // 730
                    MyCommand2.Parameters.AddWithValue("ContextId", 2); // 2
                    MyCommand2.Parameters.AddWithValue("CurrecyId", 0); // 0

                    int j = 0;

                    foreach (var item in myInventory._items)
                    {
                        var desc = myInventory._descriptions[item.Value.descriptionid];

                        MyCommand2.Parameters.AddWithValue("AssetId" + j, item.Value.assetid); // base
                        MyCommand2.Parameters.AddWithValue("ClassId" + j, desc.classid); // base
                        MyCommand2.Parameters.AddWithValue("DescriptionId" + j, item.Value.descriptionid);

                        MyCommand2.Parameters.AddWithValue("itname" + j, desc.name_hash); // base
                        MyCommand2.Parameters.AddWithValue("itimg" + j, "https://steamcommunity-a.akamaihd.net/economy/image/class/730/" + desc.classid + "/150fx125f"); // alone
                        j++;
                    }

                    using (MySqlDataReader MyReader2 = MyCommand2.ExecuteReader())
                    {
                        while (MyReader2.Read())
                        {
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
            deleteItemsFromDatabase(getSoldAssetIds(), true);
        }

        /// <summary>
        /// Function which empty commision table. (it should be done before updating it with steam inventory)
        /// </summary>
        private void clearTable()
        {
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
            try
            {
                string sql = "DELETE FROM commision_table";
                
                MySqlCommand MyCommand2 = new MySqlCommand(sql, MyConn2);
                MySqlDataReader MyReader2;
                MyConn2.Open();
                MyReader2 = MyCommand2.ExecuteReader();     // Here our query will be executed and data saved into the database.
                while (MyReader2.Read())
                { }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            MyConn2.Close();
        }

        /// <summary>
        /// Function which returns array of asset ids from commision table (items which should be deleted
        /// from commision table after updating it).
        /// </summary>
        /// <returns></returns>
        private Int64[] getSoldAssetIds()
        {
            Int64[] asset_ids;
            System.Data.DataSet ds = new System.Data.DataSet();
            MySqlConnection conn = new MySqlConnection(MyConnection2);

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT assetid FROM temp_commision";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            conn.Close();

            if (ds.Tables.Count != 0)
            {
                asset_ids = new Int64[ds.Tables[0].Rows.Count];
                for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                    asset_ids[i] = Convert.ToInt64(ds.Tables[0].Rows[i]["assetid"]);
            }
            else
                asset_ids = new Int64[0];

            return asset_ids;
        }

        /// <summary>
        /// Function designed to delete items from commision table and store it into temp table.
        /// This function is called whenever some offer is sent to user, it delete's items (look by assetids)
        /// from commision table, and all deleted data stores into temp table (if offer is declined, canceled, expired..).
        /// </summary>
        /// <param name="asset_ids"></param>
        private void deleteItemsFromCommision(Int64[] asset_ids)
        {
            System.Data.DataSet ds = readItems(asset_ids, true); // flag = true -> read main database (not temp)
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
                return;

            deleteItemsFromDatabase(asset_ids, true); // actually delete items from database
            writeItemsToDatabase(ds, false); // flag = false -> write to temp database
        }

        /// <summary>
        /// This function is designed to return items from temp table into commision table.
        /// It is called when offer is declined, canceled, expired or so...
        /// </summary>
        /// <param name="asset_ids"></param>
        private void returnItemsToDatabase(Int64[] asset_ids)
        {
            System.Data.DataSet ds = readItems(asset_ids, false); // read from temp table
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
                return;

            deleteItemsFromDatabase(asset_ids, false); // delete in temp table
            writeItemsToDatabase(ds, true); // write to commision table
        }

        /// <summary>
        /// Function which return's dataset of item information (reading from commision or temp table, depending
        /// on flag). It is used as a side function for saving data before deleting it.
        /// 
        /// If flag is set to true it will read commision table else it will read temp table
        /// </summary>
        /// <param name="asset_ids"></param>
        /// <param name="flag"></param>
        /// <returns></returns>
        private System.Data.DataSet readItems(Int64[] asset_ids, bool flag)
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            if (asset_ids.Length == 0)
                return ds;

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM " + (flag ? "commision_table" : "temp_commision") + " WHERE AssetId in (";
                for (int i = 0; i < asset_ids.Length; i++)
                    sql += asset_ids[i] + (i == asset_ids.Length - 1 ? ");" : ", ");

                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                return ds;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
                Console.WriteLine("Failed in database selection");
            }

            conn.Close();
            return ds;
        }

        /// <summary>
        /// Function which acctually deletes rows from commision (and temp) table.
        /// If flag is true it will delete from commision table else from temp table.
        /// </summary>
        /// <param name="asset_ids"></param>
        /// <param name="flag"></param>
        private void deleteItemsFromDatabase(Int64[] asset_ids, bool flag)
        {
            if (asset_ids.Length == 0)
            {
                Console.WriteLine("No items for delete");
                return;
            }

            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
            try
            {
                string sql = "DELETE FROM " + (flag ? "commision_table" : "temp_commision") + " WHERE AssetId in (";
                for (int i = 0; i < asset_ids.Length; i++)
                    sql += asset_ids[i] + (i == asset_ids.Length - 1 ? ");" : ", ");

                MySqlCommand MyCommand2 = new MySqlCommand(sql, MyConn2);
                MySqlDataReader MyReader2;
                MyConn2.Open();
                MyReader2 = MyCommand2.ExecuteReader();     // Here our query will be executed and data saved into the database.
                while (MyReader2.Read())
                { }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            MyConn2.Close();
        }

        /// <summary>
        /// Function which will write already read data to a table. It is used when deleting from commision table
        /// for caching items into temp table, or when clearing items from temp table (offer accepted).
        /// If flag is true it will write into commision table, else into temp table.
        /// </summary>
        /// <param name="ds"></param>
        /// <param name="flag"></param>
        private void writeItemsToDatabase(System.Data.DataSet ds, bool flag)
        {
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
                return;

            try
            {
                using (MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2))
                {
                    //Console.WriteLine("In create offerItem ENtry");
                    MyConn2.Open();

                    string Query = "insert into " + (flag ? "commision_table" : "temp_commision") + "(whoseitem, Amount, AppId, AssetId, ContextId, CurrecyId, ClassId, DescriptionId, itname, itimg) values ";

                    // put all items from one trade into one query (not one query per item)
                    for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                        Query += "(@whoseitem, @Amount, @AppId, @AssetId" + i + ", @ContextId, @CurrecyId, @ClassId"
                                    + i + ", @DescriptionId" + i + ", @itname" + i + ", @itimg" + i + ")" + (i == ds.Tables[0].Rows.Count - 1 ? ";" : ",");

                    MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);

                    // static parameters (same for each item)
                    MyCommand2.Parameters.AddWithValue("whoseitem", "their"); // "their"
                    MyCommand2.Parameters.AddWithValue("Amount", 1); // 1
                    MyCommand2.Parameters.AddWithValue("AppId", 730);  // 730
                    MyCommand2.Parameters.AddWithValue("ContextId", 2); // 2
                    MyCommand2.Parameters.AddWithValue("CurrecyId", 0); // 0

                    for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                    {
                        MyCommand2.Parameters.AddWithValue("AssetId" + i, ds.Tables[0].Rows[i]["AssetId"]); // base
                        MyCommand2.Parameters.AddWithValue("ClassId" + i, ds.Tables[0].Rows[i]["ClassId"]); // base
                        MyCommand2.Parameters.AddWithValue("DescriptionId" + i, ds.Tables[0].Rows[i]["DescriptionId"]);

                        MyCommand2.Parameters.AddWithValue("itname" + i, ds.Tables[0].Rows[i]["itname"]); // base
                        MyCommand2.Parameters.AddWithValue("itimg" + i, ds.Tables[0].Rows[i]["itimg"]); // alone
                    }

                    using (MySqlDataReader MyReader2 = MyCommand2.ExecuteReader())
                    {
                        while (MyReader2.Read())
                        {
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
        }

        #endregion
    }
}
