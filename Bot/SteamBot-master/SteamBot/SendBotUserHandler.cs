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
    class SendBotUserHandler : UserHandler
    {
        private System.Timers.Timer onTimedTimer;

        #region SimpleUserHandler Overrides

        public TF2Value AmountAdded;

        public SendBotUserHandler (Bot bot, SteamID sid) : base(bot, sid) {}

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
        /// Method will be called on each TradeOffer
        /// Checks offer, if items are correct, and partner sending is bot 2 or bot 3 it accepts it
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
                if (offer.PartnerSteamId.ConvertToUInt64() == (ulong)botId2 || offer.PartnerSteamId.ConvertToUInt64() == (ulong)botId3)
                    if (myItemList.Count == 0 && theirItemList.Count > 0 && csgoItems)
                    {
                        do
                        { }
                        while (!offer.Accept()); // retries to accept trade offer, if steam throws error upon accepting.
                    }
                    else
                        offer.Decline();
                else
                    offer.Decline();
            }
            catch (Exception ex)
            {
                Console.Write(ex.Message);
            }
        }

        /// <summary>
        /// Called when the bot is fully logged in.
        /// It starts one timer which should control bot 4 and 5
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
            }
        }

        /// <summary>
        /// This function is designed to check database and send items where it should be sent.
        /// It checks database (senditem) and then sends all of items to partners (winner and commision bot).
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        protected virtual void OnTimed(object sender, System.EventArgs e)
        {
            // Console.WriteLine("Send Bot [#{0}]: in OnTimed", Bot.DisplayName == botName4 ? 4 : 5);
            
            // reset timer's time
            onTimedTimer.Stop();

            System.Data.DataSet ds = checkSendItem();

            if (ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                onTimedTimer.Start();
                return;
            }

            // for each partner in each round
            foreach (System.Data.DataRow row in ds.Tables[0].Rows)
            {
                // Console.WriteLine("Checking trade with partner {0}, (in round {1})", Convert.ToString(row["partnerid"]), Convert.ToString(row["roundid"]));
                System.Data.DataSet ds2 = getSendItem(Convert.ToInt64(row["partnerid"]), Convert.ToInt64(row["roundid"]));
                SteamID otherSteamId = new SteamID((ulong)Convert.ToInt64(row["partnerid"]));
                var offer = Bot.NewTradeOffer(otherSteamId);

                IEnumerable<long> contextIds = new long[] { 2 };
                GenericInventory myInventory = new GenericInventory(Bot.SteamWeb);

                SteamID mySteamID;
                if (Bot.DisplayName == botName4)
                    mySteamID = new SteamID((ulong)botId4);
                else //if (Bot.DisplayName == botName5)
                    mySteamID = new SteamID((ulong)botId5);

                myInventory.loadImplementation(730, contextIds, mySteamID);

                removeBotItemSendEntry(myInventory._items); // delete all items from botitemsend except myInventory._items
                List<long> listBotItemSend = getBotItemSendEntry(); // get all items from botitemsend table which match myInventry._items

                List<long> used = new List<long>();
                int itemCount = 0;
                bool isfound = false;
                // for each item in concrete partnerid/roundid combination.
                foreach (System.Data.DataRow row2 in ds2.Tables[0].Rows)
                {
                    isfound = false;
                    foreach (var item in myInventory._items)
                        // find all items from myInventor._items which are not used and which are not already sent
                        if (used.Contains((long)item.Value.assetid) == false && listBotItemSend.Contains((long)item.Value.assetid) == false)
                        {
                            Int64 classiD = Convert.ToInt64(item.Value.descriptionid.Substring(0, item.Value.descriptionid.IndexOf("_")));
                            if (classiD == Convert.ToInt64(row2["ClassId"].ToString()))
                            {
                                isfound = true;
                                offer.Items.AddMyItem(730, 2, (long)item.Value.assetid);
                                itemCount = itemCount + 1;
                                used.Add((long)item.Value.assetid);
                                break;
                            }
                        }
                    if (isfound == false)
                        foreach (var desc in myInventory._descriptions)
                            if (desc.Value.name_hash.Equals(Convert.ToString(row2["itname"])))
                            {
                                MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
                                try
                                {
                                    string Query = "update senditem set classid=" + desc.Value.classid + " where classid=" + row2["ClassId"].ToString() + " and isactive=1 and  roundid=" + row2["roundid"] + " and assetid="+ row2["assetid"];
                                    MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
                                    MySqlDataReader MyReader2;
                                    MyConn2.Open();
                                    MyReader2 = MyCommand2.ExecuteReader();     // Here our query will be executed and data saved into the database.



                                }

                                catch (Exception ex)
                                {
                                    Console.WriteLine(ex.ToString());
                                }

                                MyConn2.Close();

                                break;
                            }
                }

                if (offer.Items.NewVersion && itemCount > 0 && itemCount == ds2.Tables[0].Rows.Count)
                {
                    string newOfferId;
                    string token = "";
                    if ((long)otherSteamId.ConvertToUInt64() == botId1) // commision bot
                        token = getBotToken(otherSteamId);
                    else
                        token = getUserToken(otherSteamId);

                    if (token != "")
                    {
                        string strMessage = "Round " + Convert.ToString(row["roundid"]) + " winning skins from csgowheels.com";
                        bool resultOffer = false;
                        try
                        {
                            resultOffer = offer.SendWithToken(out newOfferId, token, strMessage);

                            if (resultOffer == true)
                            {
                                Bot.AcceptAllMobileTradeConfirmations();
                                Log.Success("Trade offer sent. Offer ID: \"{0}\" SteamId: \"{1}\"", newOfferId, (otherSteamId.ConvertToUInt64() == Convert.ToUInt64(botId4) || otherSteamId.ConvertToUInt64() == Convert.ToUInt64(botId5) ? (otherSteamId.ConvertToUInt64() == Convert.ToUInt64(botId4) ? "CSGOWHEELS[#4]" : "CSGOWHEELS[#5]") : otherSteamId.ToString()));
                                Bot.TryGetTradeOffer(newOfferId, out offer);

                                updateSendItem(ds2.Tables[0]);
                                createBotItemSendEntry(used);
                            }
                            else
                                updateSendItemCount(ds2.Tables[0]); // if offer not sent propertly increment trycount
                        }
                        catch (Exception)
                        {
                            updateSendItemCount(ds2.Tables[0]); // if offer sending failed, increment trycount
                        }
                    }
                }
            }
            onTimedTimer.Start();
        }

        #region Database manipulation (decrypted)

        /// <summary>
        /// This function returns all information from senditem table, about rows which affects
        /// this bot. It looks for rows which are marked as appropriate recieve bot.
        /// 
        /// It returns one row for each roundid (and botid) for each partnerID with which should be
        /// organized trade...
        /// </summary>
        /// <returns></returns>
        private System.Data.DataSet checkSendItem()
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                Int64 steamid = 0;

                if (Bot.DisplayName == botName4)
                    steamid = botId2;
                else if (Bot.DisplayName == botName5)
                    steamid = botId3;

                string sql = "SELECT partnerid,botid,roundid FROM senditem where isactive=1 and botid=" + steamid.ToString() + " group by partnerid,botid,roundid";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                return ds;
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
            conn.Close();

            return ds;
        }

        /// <summary>
        /// Returns all rows from senditem table which match partnerID, and roundID sent as parameters, botID 
        /// decided depending on Bot, activity of row (isActive) and trycount??. 
        /// 
        /// Gets list of items to send (concrete items for one partnerid and roundid)
        /// </summary>
        /// <param name="partnetId"></param>
        /// <param name="roundid"></param>
        /// <returns></returns>
        private System.Data.DataSet getSendItem(Int64 partnetId, Int64 roundid)
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                Int64 steamid = 0;
                if (Bot.DisplayName == botName4)
                    steamid = botId2;
                else if (Bot.DisplayName == botName5)
                    steamid = botId3;

                string sql = "SELECT * FROM senditem where isactive=1 and trycount<3 and botid=" + steamid.ToString() 
                                + " and partnerid=" + partnetId.ToString() + " and roundid = " + roundid.ToString();
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                return ds;
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }

            conn.Close();

            return ds;
        }

        /// <summary>
        /// Deletes all rows from botitemsend table which is related to current bot, except ones
        /// which are sent in dictionary as a parameter.
        /// </summary>
        /// <param name="items"></param>
        private void removeBotItemSendEntry(Dictionary<ulong, GenericInventory.Item> items)
        {
            if (items.Count == 0)
                return;

            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);
            try
            {
                long tmpBotId;
                if (Bot.DisplayName == botName4)
                    tmpBotId = botId4;
                else //if (Bot.DisplayName == botName5)
                    tmpBotId = botId5;

                string Query = "delete from botitemsend where botId64='" + tmpBotId + "' and AssetId not in (";
                string tmpDelete = "";

                foreach (var item in items)
                    tmpDelete = tmpDelete + "'" + item.Value.assetid + "',";

                tmpDelete = tmpDelete.Trim(',');
                Query = Query + tmpDelete + ")";

                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
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
        /// Retrieves all data from botitemsend table where botid matches current bot's id
        /// </summary>
        /// <returns></returns>
        private List<long> getBotItemSendEntry()
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();

            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                long tmpBotId;
                if (Bot.DisplayName == botName4)
                    tmpBotId = botId4;
                else //if (Bot.DisplayName == botName5)
                    tmpBotId = botId5;

                string sql = "SELECT * FROM botitemsend where botId64='" + tmpBotId.ToString() + "'";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
            conn.Close();

            List<long> list = new List<long>();

            foreach (System.Data.DataRow row in ds.Tables[0].Rows)
                list.Add((long)row["AssetId"]);

            return list;
        }

        /// <summary>
        /// Writes into botitemsend all items which bot sent (recieves items assetIDs as parameter)
        /// </summary>
        /// <param name="list"></param>
        private void createBotItemSendEntry(List<long> list)
        {
            // check for count
            if (list.Count == 0)
                return;

            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);

            try
            {
                string Query = "insert into botitemsend (botId64, AssetId, date_time) values ";
                string tmpInsert = "";
                //This is command class which will handle the query and connection object.

                long tmpBotId;

                if (Bot.DisplayName == botName4)
                    tmpBotId = botId4;
                else //if (Bot.DisplayName == botName5)
                    tmpBotId = botId5;

                for (int i = 0; i < list.Count; i++)
                    tmpInsert = tmpInsert + " ('" + tmpBotId + "','" + list[i] + "', Now()),";

                tmpInsert = tmpInsert.Trim(',');
                Query = Query + tmpInsert;

                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
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
        /// Deactivate rows in senditem finding by id's (sent as parameter).
        /// This function is called when trade is successuful.
        /// </summary>
        /// <param name="dt"></param>
        private void updateSendItem(System.Data.DataTable dt)
        {
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);

            if (dt.Rows.Count == 0)
                return;

            try
            {
                string Query = "update senditem set isactive = 0 where id in (";
                string tmpQuery = "";
                foreach (System.Data.DataRow row in dt.Rows)
                    tmpQuery = tmpQuery + "'" + row["id"].ToString() + "',";
                tmpQuery = tmpQuery.TrimEnd(',') + ")";
                Query = Query + tmpQuery;

                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
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
        /// Updates trycount variable in senditem table, incrementing it by one.
        /// This function should be called when trade is unsuccessuful.
        /// </summary>
        /// <param name="dt"></param>
        private void updateSendItemCount(System.Data.DataTable dt)
        {
            MySql.Data.MySqlClient.MySqlConnection MyConn2 = new MySql.Data.MySqlClient.MySqlConnection(MyConnection2);

            if (dt.Rows.Count == 0)
                return;

            try
            {
                string Query = "update senditem set trycount = trycount + 1 where id in (";
                string tmpQuery = "";
                foreach (System.Data.DataRow row in dt.Rows)
                    tmpQuery = tmpQuery + "'" + row["id"].ToString() + "',";

                tmpQuery = tmpQuery.TrimEnd(',') + ")";
                Query = Query + tmpQuery;

                MySqlCommand MyCommand2 = new MySqlCommand(Query, MyConn2);
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

        #endregion
    }
}
