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
    class RecieveBotUserHandler : UserHandler
    {
        private readonly string updateTradeCode = "QK6A-JI6S-7ETR-0A6C";
        private readonly string startJackpotCode = "N9TT-9G0A-B7FQ-RANC";

        private System.Timers.Timer onTimedTimer;
        private System.Timers.Timer checkOfferConfirmTimer;
        private System.Timers.Timer getOfferStateTimer;

        #region SimpleUserHandler Overrides

        public TF2Value AmountAdded;

        public RecieveBotUserHandler (Bot bot, SteamID sid) : base(bot, sid) {}

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

        /// <summary>
        /// Method will be called on each TradeOffer,
        /// Offer is always declined (bot 2 and 3 never recieve any offer...)
        /// </summary>
        /// <param name="offer"></param>
        public override void OnNewTradeOffer(TradeOffer offer)
        {
            try
            {
                offer.Decline();
            }
            catch (Exception ex)
            {
                Console.Write(ex.Message);
            }
        }

        /// <summary>
        /// Called when the bot is fully logged in.
        /// Starts all timers needed for Recieve bots.
        /// </summary>
        public override void OnLoginCompleted()
        {
            if (onTimedTimer == null || !onTimedTimer.Enabled)
            {
                onTimedTimer = new System.Timers.Timer();
                onTimedTimer.Elapsed += new System.Timers.ElapsedEventHandler(OnTimed);
                onTimedTimer.Interval = 15000;
                onTimedTimer.AutoReset = false;
                onTimedTimer.Enabled = true;
                onTimedTimer.Start();
            }

            // database check for tradeoffer(Gara)
            if (checkOfferConfirmTimer == null || !checkOfferConfirmTimer.Enabled)
            {
                checkOfferConfirmTimer = new System.Timers.Timer();
                checkOfferConfirmTimer.Elapsed += new System.Timers.ElapsedEventHandler(checkOfferConfirm);
                checkOfferConfirmTimer.Interval = 5000;
                checkOfferConfirmTimer.AutoReset = false;
                checkOfferConfirmTimer.Enabled = true;
                checkOfferConfirmTimer.Start();
            }

            // timer for checking accepted offers
            if (getOfferStateTimer == null || !getOfferStateTimer.Enabled)
            {
                getOfferStateTimer = new System.Timers.Timer();
                getOfferStateTimer.Elapsed += new System.Timers.ElapsedEventHandler(getOfferState);
                getOfferStateTimer.Interval = 5000;
                getOfferStateTimer.AutoReset = false;
                getOfferStateTimer.Enabled = true;
                getOfferStateTimer.Start();
            }
        }

        /// <summary>
        /// This function is designed to check database and send items where it should
        /// It works for bot2 and 3 by sending all items to 4 and 5.
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        protected virtual void OnTimed(object sender, System.EventArgs e)
        {
            // Console.WriteLine("Recieve Bot [#{0}]: in OnTimed", Bot.DisplayName == botName2 ? 2 : 3);

            // restart timer time
            onTimedTimer.Stop();

            SteamID mySteamID;
            SteamID otherSteamId;
            if (Bot.DisplayName == botName2)
            {
                otherSteamId = new SteamID((ulong)botId4);
                mySteamID = new SteamID((ulong)botId2);
            }
            else
            {
                otherSteamId = new SteamID((ulong)botId5);
                mySteamID = new SteamID((ulong)botId3);
            }

            var offer = Bot.NewTradeOffer(otherSteamId);
            
            IEnumerable<long> contextIds = new long[] { 2 };
            GenericInventory myInventory = new GenericInventory(Bot.SteamWeb);

            myInventory.loadImplementation(730, contextIds, mySteamID);

            if (myInventory._items.Count > 0)
            {
                foreach (var item in myInventory._items)
                    offer.Items.AddMyItem(730, 2, (long)item.Value.assetid);

                if (offer.Items.NewVersion)
                {
                    string newOfferId;
                    string token = getBotToken(otherSteamId);
                    if (token != "")
                    { 
                        var escrow_duration = Bot.GetEscrowDuration(otherSteamId, token);
                        if (escrow_duration.DaysTheirEscrow == 0)       // this escrow is not necessary and could be removed for optimizations.
                        if (offer.SendWithToken(out newOfferId, token))
                        {
                            Bot.AcceptAllMobileTradeConfirmations();
                            Log.Success("Trade offer sent. Offer ID: \"{0}\" SteamId: \"{1}\"", newOfferId, (otherSteamId.ConvertToUInt64() == Convert.ToUInt64(botId4) ? "CSGOWHEELS[#4]" : "CSGOWHEELS[#5]"));
                            Bot.TryGetTradeOffer(newOfferId, out offer);
                        }
                    }
                }
            }
            onTimedTimer.Start();
        }

        #region OnTimed helper functions (empty)

        #endregion

        /// <summary>
        /// This function goes through all offers
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        protected virtual void checkOfferConfirm(object sender, System.EventArgs e)
        {
            // Console.WriteLine("Recieve Bot [#{0}]: in checkOfferConfirm", Bot.DisplayName == botName2 ? 2 : 3);

            // reset timer time
            checkOfferConfirmTimer.Stop();

            System.Data.DataSet ds = checkOfferDataBase();
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                checkOfferConfirmTimer.Start();
                return;
            }

            try
            {
                List<Int64> asset_id = new List<Int64>();
                int total_items = 0, security_code = 0, new_offer = 0;
                Int64 steamid =0;

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
                checkOfferConfirmTimer.Start();
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
                checkOfferConfirmTimer.Start();
            }
        }

        #region CheckOfferConfirm helper functions

        /// <summary>
        /// Function designed to handle database connection, all it does
        /// it returns data from new_offer table where is_checked variable is set to 0
        /// (which means that those offers should be checked (sent by bots to user requested them)
        /// </summary>
        /// <returns></returns>
        private System.Data.DataSet checkOfferDataBase()
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM new_offer WHERE isChecked=0 AND botid=" + (Bot.DisplayName == botName2 ? botId2 : botId3);
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
        /// It updates new_offer table, setting isChecked on 1, offerId on appropriate offerID, botId on executing bot's
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
                    cmd.CommandText = "UPDATE new_offer SET isChecked=1,offerid=" + newsOfferId + ", botid='" + (Bot.DisplayName == botName2 ? botId2 : botId3) + "'"
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
                if (!offers2.Items.AddTheirItem(730, 2, tot_items[i]))
                {
                    Log.Error("Recieve Bot [#{0}]: Failed adding item to offer. (user: {1}, assetID: {2})", Bot.DisplayName == botName2 ? 2 : 3, steamid, tot_items[i]);
                    deleteOfferFromDataBase(steamid, security_code);
                    return;
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
        /// Method deletes all offers which offerids are invalid ('failed')
        /// </summary>
        private void deleteOfferFromDataBase(long steamid, int security_code)
        {
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
            try
            {
                string Query = "DELETE FROM new_offer WHERE security_code=" + security_code + " AND steamid=" + steamid;

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
        /// For each accepted trade offer it creates database entry (trade and tradeitems tables), 
        /// deletes those offers from new_trade table.
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        protected virtual void getOfferState(object sender, System.EventArgs e)
        {
            // Console.WriteLine("Recieve Bot [#{0}]: in getOfferState", Bot.DisplayName == botName2 ? 2 : 3);

            // reset timer time
            getOfferStateTimer.Interval = 5000;
            getOfferStateTimer.Stop();

            System.Data.DataSet ds = getOfferDataBase();
            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                getOfferStateTimer.Start();
                return;
            }

            bool isAnyThingAccepted = false, isAnythingDeclined = false;
            List<string> offers = new List<string>();

            for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                if (!offers.Contains(Convert.ToString(ds.Tables[0].Rows[i]["offerid"])))
                    offers.Add(Convert.ToString(ds.Tables[0].Rows[i]["offerid"]));

            // check if commision is locked
            if (isLocked())
            {
                getOfferStateTimer.Interval = 2000;
                getOfferStateTimer.Stop();
                getOfferStateTimer.Start();
                return;
            }

            foreach (string offerID in offers)
            {
                TradeOfferState tradeState = Bot.getTradeOfferState(offerID);
                if (tradeState == TradeOfferState.TradeOfferStateAccepted)
                {
                    // lock commision on first accepted offer.
                    lockCommision();

                    // Write offer into database (and all items), and check offer accepted (in new_offer)
                    createOfferEntry(offerID);
                    createOfferItemEntry(offerID);
                    setAccepted(offerID);
                    isAnyThingAccepted = true;
                }
                else if (tradeState == TradeOfferState.TradeOfferStateDeclined
                        || tradeState == TradeOfferState.TradeOfferStateCanceled
                        || tradeState == TradeOfferState.TradeOfferStateExpired
                        || tradeState == TradeOfferState.TradeOfferStateInvalid
                        || tradeState == TradeOfferState.TradeOfferStateInvalidItems)
                {
                    // delete offer from database
                    setAccepted(offerID); // check it for delete
                    isAnythingDeclined = true;
                }
            }

            // If there was any offers worked through, try deleting all accepted offers (from new_offer)
            // and notifie server (php) that trade offers were accepted (jackpot maybe should start)
            if (isAnyThingAccepted || isAnythingDeclined)
            {
                isAnythingDeclined = false;
                deleteOfferDataBase();

                // update trade and start jackpot
                if (isAnyThingAccepted)
                {
                    System.Net.WebClient client = new System.Net.WebClient();
                    string downloadString;
                    int i = 0;

                    do
                    {
                        do
                        {
                            downloadString = client.DownloadString(updateTradeUrl);
                            if (i++ > 0)
                                Log.Error("Recieve Bot [#{0}] Failed in updateTrade request.", Bot.DisplayName == botName2 ? 2 : 3);
                        } while (downloadString != updateTradeCode);
                        i = 0;
                    
                        downloadString = client.DownloadString(startJackpotUrl);
                        if (i++ > 0)
                        {
                            Log.Error("Recieve Bot [#{0}] Failed in startJackpot request.", Bot.DisplayName == botName2 ? 2 : 3);
                            lockCommision();
                        }
                    } while (downloadString != startJackpotCode);

                    isAnyThingAccepted = false;
                }
            }

            // some php stuff for chat (not at all related to this method, only here because of timer use)
            if (Bot.DisplayName == botName2)
            {
                System.Net.WebClient client = new System.Net.WebClient();
                string downloadString = client.DownloadString(checkMutedUrl);
            }

            getOfferStateTimer.Start();

        }

        #region getOfferState helper functions

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

                string sql = "SELECT * FROM new_offer WHERE botid='" + (Bot.DisplayName == botName2 ? botId2 : botId3) + "'" 
                                +" AND isChecked=1 AND isaccepted=0" + (offerID == "" ? "" : " AND offerid=" + offerID);
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
        /// Using offerID string sent to function as a parameter, it finds concrete offer in new_offer database
        /// and those data worked through writes into trade table (required for other parts of app (server) to work propertly)
        /// </summary>
        /// <param name="offerID"></param>
        private void createOfferEntry(String offerID)
        {
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);

            try
            {
                //Console.WriteLine("In creating offer entry");
                System.Data.DataSet ds = getOfferDataBase(offerID);

                if (ds.Tables[0].Rows.Count == 0)
                    return;

                string Query = "insert into trade(TradeOfferId, botId64, partnerId, partnerId64, partnerAccountId, IsOurOffer, TimeCreated, ExpirationTime, TimeUpdated, Createddate)" +
                                " values (@TradeOfferId, @botId64,  @partnerId, @partnerId64, @partnerAccountId, @IsOurOffer, @TimeCreated, @ExpirationTime, @TimeUpdated, @Createddate)";

                //This is command class which will handle the query and connection object.
                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);

                MyCommand2.Parameters.AddWithValue("TradeOfferId", offerID); // base
                MyCommand2.Parameters.AddWithValue("botId64", ds.Tables[0].Rows[0]["botid"]);
                MyCommand2.Parameters.AddWithValue("partnerId", ds.Tables[0].Rows[0]["steamid"]); // base
                MyCommand2.Parameters.AddWithValue("partnerId64", Convert.ToInt64(ds.Tables[0].Rows[0]["steamid"])); // base
                MyCommand2.Parameters.AddWithValue("partnerAccountId", ds.Tables[0].Rows[0]["accountid"]); // look...
                MyCommand2.Parameters.AddWithValue("IsOurOffer", 0);  // 0
                MyCommand2.Parameters.AddWithValue("TimeCreated", ds.Tables[0].Rows[0]["time_created"]); // base
                MyCommand2.Parameters.AddWithValue("ExpirationTime", Convert.ToInt32(ds.Tables[0].Rows[0]["time_created"]) + 60000); // ...
                MyCommand2.Parameters.AddWithValue("TimeUpdated", 0); // 0
                MyCommand2.Parameters.AddWithValue("Createddate", ds.Tables[0].Rows[0]["time_created"]); // timeCreated

                MySqlDataReader MyReader2;
                MyConn2.Open();
                MyReader2 = MyCommand2.ExecuteReader();     // Here our query will be executed and data saved into the database.
                while (MyReader2.Read())
                {
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }

            MyConn2.Close();
        }

        /// <summary>
        /// Using offerID string sent to function as a parameter, it finds concrete offer in new_offer database
        /// and those data worked through writes into tradeitems table (required for other parts of app (server) to work propertly)
        /// for each item from offer (with offerID sent as parameter) it creates row in tradeitems table
        /// </summary>
        /// <param name="offerID"></param>
        private void createOfferItemEntry(String offerID)
        {
            try
            {
                using (MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2))
                {
                    //Console.WriteLine("In create offerItem ENtry");
                    MyConn2.Open();

                    System.Data.DataSet ds = getOfferDataBase(offerID);
                    if (ds.Tables[0].Rows.Count == 0)
                        return;

                    string Query = "insert into tradeitems(tradeid, whoseitem, Amount, AppId, AssetId, ContextId, CurrecyId, ClassId, InstanceId, itname, itimg, itprice, totamt) values ";

                    // put all items from one trade into one query (not one query per item)
                    for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                        Query += "(@tradeid" +  i + ", @whoseitem, @Amount, @AppId, @AssetId" +  i + ", @ContextId, @CurrecyId, @ClassId" 
                                    +  i + ", @InstanceId" +  i + ", @itname" +  i + ", @itimg" +  i + ", @itprice" +  i 
                                    + ", @totamt" +  i + ")" + (i == ds.Tables[0].Rows.Count - 1 ? ";" : ",");

                    MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);

                    // static parameters (same for each item)
                    MyCommand2.Parameters.AddWithValue("whoseitem", "their"); // "their"
                    MyCommand2.Parameters.AddWithValue("Amount", 1); // 1
                    MyCommand2.Parameters.AddWithValue("AppId", 730);  // 730
                    MyCommand2.Parameters.AddWithValue("ContextId", 2); // 2
                    MyCommand2.Parameters.AddWithValue("CurrecyId", 0); // 0

                    for (int i = 0; i < ds.Tables[0].Rows.Count; i++)
                    {
                        MyCommand2.Parameters.AddWithValue("tradeid" + i, ds.Tables[0].Rows[i]["offerid"]); // base
                        MyCommand2.Parameters.AddWithValue("AssetId" + i, ds.Tables[0].Rows[i]["assetid"]); // base
                        MyCommand2.Parameters.AddWithValue("ClassId" + i, ds.Tables[0].Rows[i]["classid"]); // base
                        MyCommand2.Parameters.AddWithValue("InstanceId" + i, ds.Tables[0].Rows[i]["instanceid"]); // base

                        MyCommand2.Parameters.AddWithValue("itname" + i, ds.Tables[0].Rows[i]["itname"]); // base
                        MyCommand2.Parameters.AddWithValue("itimg" + i, "https://steamcommunity-a.akamaihd.net/economy/image/class/730/" + ds.Tables[0].Rows[i]["classid"] + "/150fx125f"); // alone
                        MyCommand2.Parameters.AddWithValue("itprice" + i, ds.Tables[0].Rows[i]["itprice"]); // base
                        MyCommand2.Parameters.AddWithValue("totamt" + i, ds.Tables[0].Rows[i]["itprice"]); // base
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
                    cmd.CommandText = "UPDATE new_offer SET isAccepted = 1 WHERE offerid='" + offerID + "'";
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
                string Query = "delete from new_offer where isAccepted=1"; // or UNIX_TIMESTAMP() - time_created > 1800";

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
                            offer.Cancel();
                            setAccepted(offer.TradeOfferId); // Convert.ToString(row["offerid"]);
                            Log.Info("Offer expired (Offer id: {0})", row["offerid"]);
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

                string sql = "SELECT offerid FROM new_offer WHERE offerid <> 0 AND isAccepted = 0 AND isChecked = 1 AND UNIX_TIMESTAMP() - time_created > 1800 GROUP BY offerid";
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

        #region Other

        /// <summary>
        /// Locks commision for other users (bots), it should be unlocked after startjackpot.php does its work
        /// </summary>
        private void lockCommision()
        {
            try
            {
                using (MySqlConnection cn = new MySqlConnection(MyConnection2))
                {
                    MySqlCommand cmd = new MySqlCommand();
                    cmd.Connection = cn;
                    cmd.CommandText = "UPDATE locked SET locked = 1 WHERE id = 1";
                    cn.Open();
                    int numRowsUpdated = cmd.ExecuteNonQuery();
                    cmd.Dispose();
                }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
                Log.Error("Could not lock commision");
            }
        }

        /// <summary>
        /// Checks Lock table (if commision is locked...)
        /// </summary>
        /// <returns>True if is locked, false otherwise</returns>
        private bool isLocked()
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM locked WHERE id = 1";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                if (ds.Tables.Count != 0 && ds.Tables[0].Rows.Count != 0)
                {
                    if (Convert.ToInt16(ds.Tables[0].Rows[0]["locked"]) == 1)
                        return true;
                    else
                        return false;
                }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.ToString());
            }

            conn.Close();
            return false;

        }

        #endregion

    }
}
