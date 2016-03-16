﻿using System;
using System.Linq;
using System.Security.AccessControl;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using SteamKit2;
using SteamTrade;
using SteamTrade.TradeOffer;
using MySql.Data.MySqlClient;
using System.Collections.Generic;  //Its for MySQL.

namespace SteamBot
{
    /// <summary>
    /// The abstract base class for users of SteamBot that will allow a user
    /// to extend the functionality of the Bot.
    /// </summary>
    public abstract class UserHandler
    {
        public Bot Bot { get; private set; }
        public SteamID OtherSID { get; private set; }

        private bool _lastMessageWasFromTrade;
        private Task<Inventory> otherInventoryTask;
        private TaskCompletionSource<string> _waitingOnUserResponse;

        #region Protected readonly variables

        // live variable
        protected readonly string MyConnection2 = "datasource=localhost;username=csgowhe1_axe;password=Bortezomib109.;database=csgowhe1_csgowhee;CharSet=utf8;";
        // protected readonly string MyConnection2 = "datasource=localhost;username=root;database=csgowhee_csgo;CharSet=utf8;";

        // live
        protected string updateTradeUrl = "https://csgowheels.com/updatetrade.php";
        protected string startJackpotUrl = "https://csgowheels.com/startjackpot.php";
        protected string checkMutedUrl = "https://csgowheels.com/check_muted.php";

        // developing urls
        // protected readonly string updateTradeUrl = "http://dev.csgowheels.com/updatetrade.php";
        // protected readonly string startJackpotUrl = "http://dev.csgowheels.com/startjackpot.php";
        // protected readonly string checkMutedUrl = "http://dev.csgowheels.com/check_muted.php";
      //  protected readonly string updateTradeUrl = "http://127.0.0.1:8080/updatetrade.php";
        // protected readonly string startJackpotUrl = "http://127.0.0.1:8080/startjackpot.php";
         // protected readonly string checkMutedUrl = "http://127.0.0.1:8080/check_muted.php";

        // live variable
        protected const Int64 botId1 = 76561198235135036;
        protected const Int64 botId2 = 76561198235134508;
        protected const Int64 botId3 = 76561198203046427;
        protected const Int64 botId4 = 76561198203224241;
        protected const Int64 botId5 = 76561198203309841;

        protected const string botName1 = "CSGOWHEELS [#1]"; // commission bot
        protected const string botName2 = "CSGOWHEELS [#2]"; // receiving bot
        protected const string botName3 = "CSGOWHEELS [#3]"; // receiving  bot
        protected const string botName4 = "CSGOWHEELS [#4]"; // sent bot
        protected const string botName5 = "CSGOWHEELS [#5]"; // sent bot

        #endregion

        protected SteamWeb SteamWeb
        {
            get
            {
                if (Bot == null || Bot.SteamWeb == null)
                {
                    throw new InvalidOperationException("You cannot use 'SteamWeb' before the Bot has been initialized!");
                }
                return Bot.SteamWeb;
            }
        }

        public UserHandler(Bot bot, SteamID sid)
        {
            Bot = bot;
            OtherSID = sid;
            GetOtherInventory();
        }

        private bool HandleWaitingOnUserResponse(string message)
        {
            if (_waitingOnUserResponse == null)
                return false;

            _waitingOnUserResponse.SetResult(message);
            _waitingOnUserResponse = null;
            return true;
        }

        /// <summary>
        /// Gets the other's inventory and stores it in OtherInventory.
        /// </summary>
        /// <example> This sample shows how to find items in the other's inventory from a user handler.
        /// <code>
        /// GetInventory(); // Not necessary unless you know the user's inventory has changed
        /// foreach (var item in OtherInventory)
        /// {
        ///     if (item.Defindex == 5021)
        ///     {
        ///         // Bot has a key in its inventory
        ///     }
        /// }
        /// </code>
        /// </example>
        public void GetOtherInventory()
        {
            otherInventoryTask = Task.Factory.StartNew(() => Inventory.FetchInventory(OtherSID, Bot.ApiKey, SteamWeb));
        }

        public Inventory OtherInventory
        {
            get
            {
                otherInventoryTask.Wait();
                return otherInventoryTask.Result;
            }
        }

        /// <summary>
        /// Gets the Bot's current trade.
        /// </summary>
        /// <value>
        /// The current trade.
        /// </value>
        public Trade Trade
        {
            get
            {
                return Bot.CurrentTrade;
            }
        }

        /// <summary>
        /// Gets the log the bot uses for convenience.
        /// </summary>
        public Log Log
        {
            get { return Bot.Log; }
        }

        /// <summary>
        /// Gets a value indicating whether the other user is admin.
        /// </summary>
        /// <value>
        /// <c>true</c> if the other user is a configured admin; otherwise, <c>false</c>.
        /// </value>
        public bool IsAdmin
        {
            get { return Bot.Admins.Contains(OtherSID); }
        }

        #region Event handlers

        /// <summary>
        /// Called when the bot is invited to a Steam group
        /// </summary>
        /// <returns>
        /// Whether to accept.
        /// </returns>
        public abstract bool OnGroupAdd();

        /// <summary>
        /// Called when the user adds the bot as a friend.
        /// </summary>
        /// <returns>
        /// Whether to accept.
        /// </returns>
        public abstract bool OnFriendAdd();

        /// <summary>
        /// Called when the user removes the bot as a friend.
        /// </summary>
        public abstract void OnFriendRemove();

        /// <summary>
        /// Called whenever a message is sent to the bot.
        /// This is limited to regular and emote messages.
        /// </summary>
        public abstract void OnMessage(string message, EChatEntryType type);

        public void OnMessageHandler(string message, EChatEntryType type)
        {
            _lastMessageWasFromTrade = false;
            if (!HandleWaitingOnUserResponse(message))
            {
                OnMessage(message, type);
            }
        }

        /// <summary>
        /// Called when the bot is fully logged in.
        /// </summary>
        public abstract void OnLoginCompleted();


        /// <summary>
        /// Called whenever a user requests a trade.
        /// </summary>
        /// <returns>
        /// Whether to accept the request.
        /// </returns>
        public abstract bool OnTradeRequest();

        /// <summary>
        /// Called when a new trade offer is received
        /// </summary>
        /// <param name="offer"></param>
        public virtual void OnNewTradeOffer(TradeOffer offer)
        {

        }

        /// <summary>
        /// Called when a chat message is sent in a chatroom
        /// </summary>
        /// <param name="chatID">The SteamID of the group chat</param>
        /// <param name="sender">The SteamID of the sender</param>
        /// <param name="message">The message sent</param>
        public virtual void OnChatRoomMessage(SteamID chatID, SteamID sender, string message)
        {

        }

        /// <summary>
        /// Called when an 'exec' command is given via botmanager.
        /// </summary>
        /// <param name="command">The command message.</param>
        public virtual void OnBotCommand(string command)
        {

        }

        /// <summary>
        /// Called when user accepts or denies bot's trade request.
        /// </summary>
        /// <param name="accepted">True if user accepted bot's request, false if not.</param>
        /// <param name="response">String response of the callback.</param>
        public virtual void OnTradeRequestReply(bool accepted, string response)
        {

        }

        /// <summary>
        /// Waits for the user to enter something into regular or trade chat, then returns it (as the result of a task)
        /// Usage: The following displays "How many do you want to buy" and stores the user's response:
        /// string userResponse = await GetUserResponse("How many do you want to buy?");
        /// 
        /// Note: calling this method causes the next user-message to NOT call OnMessage() or OnTradeMessage()
        /// </summary>
        /// <param name="message">An option message to display to the user.
        /// Sent to whichever chat (normal or trade) is currently being used.</param>
        protected virtual Task<string> GetUserResponse(string message = null)
        {
            if (message != null)
            {
                SendReplyMessage(message);
            }

            _waitingOnUserResponse = new TaskCompletionSource<string>();
            return _waitingOnUserResponse.Task;
        }

        #endregion

        #region Trade events
        // see the various events in SteamTrade.Trade for descriptions of these handlers.

        public abstract void OnTradeError(string error);

        public virtual void OnStatusError(Trade.TradeStatusType status)
        {
            string otherUserName = Bot.SteamFriends.GetFriendPersonaName(OtherSID);
            string statusMessage = (Trade != null ? Trade.GetTradeStatusErrorString(status) : "died a horrible death");
            string errorMessage = String.Format("Trade with {0} ({1}) {2}", otherUserName, OtherSID.ConvertToUInt64(), statusMessage);
            OnTradeError(errorMessage);
        }

        public abstract void OnTradeTimeout();

        public abstract void OnTradeSuccess();

        public void _OnTradeAwaitingConfirmation(long tradeOfferID)
        {
            Bot.AcceptAllMobileTradeConfirmations();
            TradeOffer tradeOffer;
            if (Bot.TryGetTradeOffer(tradeOfferID.ToString(), out tradeOffer))
            {
                if (tradeOffer.OfferState == TradeOfferState.TradeOfferStateNeedsConfirmation)
                {
                    OnTradeAwaitingConfirmation(tradeOfferID);
                }
            }            
        }

        public abstract void OnTradeAwaitingConfirmation(long tradeOfferID);

        public virtual void OnTradeClose()
        {
            Bot.CloseTrade();
        }

        public abstract void OnTradeInit();

        public abstract void OnTradeAddItem(Schema.Item schemaItem, Inventory.Item inventoryItem);

        public abstract void OnTradeRemoveItem(Schema.Item schemaItem, Inventory.Item inventoryItem);

        public void OnTradeMessageHandler(string message)
        {
            _lastMessageWasFromTrade = true;
            if (!HandleWaitingOnUserResponse(message))
            {
                OnTradeMessage(message);
            }
        }

        public abstract void OnTradeMessage(string message);

        public void OnTradeReadyHandler(bool ready)
        {
            Trade.Poll();
            OnTradeReady(ready);
        }

        public abstract void OnTradeReady(bool ready);

        public void OnTradeAcceptHandler()
        {
            Trade.Poll();
            if (Trade.OtherIsReady && Trade.MeIsReady)
            {
                OnTradeAccept();
            }
        }

        public abstract void OnTradeAccept();

        #endregion Trade events

        #region SendChatMessage methods

        private void SendMessage(Action<string> messageFunc, string message, System.Timers.Timer timer, params object[] formatParams)
        {
            try
            {
                if (timer != null)
                {
                    timer.Dispose();
                }

                message = (formatParams != null && formatParams.Any() ? String.Format(message, formatParams) : message);
                messageFunc(message);
            }
            catch (Exception ex)
            {
                Log.Error(String.Format("Error occurred when sending message.  Message: \"{0}\" \nException: {1} ", message, ex.ToString()));
            }
        }

        private void SendMessageDelayed(int delayMs, Action<string> messageFunc, string message, params object[] formatParams)
        {
            if (delayMs <= 0)
            {
                SendMessage(messageFunc, message, null, formatParams);
                return;
            }

            System.Timers.Timer timer = new System.Timers.Timer
            {
                Interval = delayMs,
                AutoReset = false
            };
            timer.Elapsed += (sender, args) => SendMessage(messageFunc, message, timer, formatParams);

            timer.Start();
        }

        /// <summary>
        /// A helper method for sending a chat message to the other user in the chat window (as opposed to the trade window)
        /// </summary>
        /// <param name="message">The message to send to the other user</param>
        /// <param name="formatParams">Optional.  The format parameters, using the same syntax as String.Format()</param>
        protected virtual void SendChatMessage(string message, params object[] formatParams)
        {
            SendMessage(SendChatMessageImpl, message, null, formatParams);
        }

        /// <summary>
        /// A helper method for sending a chat message to the other user in the chat window (as opposed to the trade window)
        /// after a given delay
        /// </summary>
        /// <param name="delayMs">The delay, in milliseconds, to wait before sending the message</param>
        /// <param name="message">The message to send to the other user</param>
        /// <param name="formatParams">Optional.  The format parameters, using the same syntax as String.Format()</param>
        protected virtual void SendChatMessage(int delayMs, string message, params object[] formatParams)
        {
            SendMessageDelayed(delayMs, SendChatMessageImpl, message, formatParams);
        }

        private void SendChatMessageImpl(string message)
        {
            Bot.SteamFriends.SendChatMessage(OtherSID, EChatEntryType.ChatMsg, message);
        }

        /// <summary>
        /// A helper method for sending a chat message to the other user in the trade window.
        /// If the trade has ended, nothing this does nothing
        /// </summary>
        /// <param name="message">The message to send to the other user</param>
        /// <param name="formatParams">Optional.  The format parameters, using the same syntax as String.Format()</param>
        protected virtual void SendTradeMessage(string message, params object[] formatParams)
        {
            SendMessage(SendTradeMessageImpl, message, null, formatParams);
        }

        /// <summary>
        /// A helper method for sending a chat message to the other user in the trade window after a given delay.
        /// If the trade has ended, nothing this does nothing
        /// </summary>
        /// <param name="delayMs">The delay, in milliseconds, to wait before sending the message</param>
        /// <param name="message">The message to send to the other user</param>
        /// <param name="formatParams">Optional.  The format parameters, using the same syntax as String.Format()</param>
        protected virtual void SendTradeMessage(int delayMs, string message, params object[] formatParams)
        {
            SendMessageDelayed(delayMs, SendTradeMessageImpl, message, formatParams);
        }

        private void SendTradeMessageImpl(string message)
        {
            if (Trade != null && !Trade.HasTradeEnded)
            {
                Trade.SendMessage(message);
            }
        }

        /// <summary>
        /// Sends a message to the user in either the chat window or the trade window, depending on which screen
        /// the user sent a message from last.  Useful for responding to commands.
        /// </summary>
        /// <param name="message">The message to send to the other user</param>
        /// <param name="formatParams">Optional.  The format parameters, using the same syntax as String.Format()</param>
        protected virtual void SendReplyMessage(string message, params object[] formatParams)
        {
            if (_lastMessageWasFromTrade && Trade != null && !Trade.HasTradeEnded)
            {
                SendTradeMessage(message, formatParams);
            }
            else
            {
                SendChatMessage(message, formatParams);
            }
        }

        /// <summary>
        /// Sends a message to the user in either the chat window or the trade window, depending on which screen
        /// the user sent a message from last, after a gven delay.  Useful for responding to commands.
        /// </summary>
        /// <param name="delayMs">The delay, in milliseconds, to wait before sending the message</param>
        /// <param name="message">The message to send to the other user</param>
        /// <param name="formatParams">Optional.  The format parameters, using the same syntax as String.Format()</param>
        protected virtual void SendReplyMessage(int delayMs, string message, params object[] formatParams)
        {
            if (_lastMessageWasFromTrade && Trade != null && !Trade.HasTradeEnded)
            {
                SendTradeMessage(delayMs, message, formatParams);
            }
            else
            {
                SendChatMessage(delayMs, message, formatParams);
            }
        }
        #endregion

        #region get token - Bot, User

        protected string getBotToken(SteamID steamId)
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();
            string token = "";
            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM bot where steamid64='" + steamId.ConvertToUInt64().ToString() + "'";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                if (ds.Tables[0].Rows.Count > 0)
                {
                    string url = ds.Tables[0].Rows[0]["url"].ToString();
                    if (url.IndexOf('=') > 0)
                    {
                        token = url.Substring(url.LastIndexOf('=') + 1);
                    }
                    token = token.Trim();
                }

            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }

            conn.Close();

            return token;
        }

        protected string getUserToken(SteamID steamId) 
        {
            MySqlConnection conn = new MySqlConnection(MyConnection2);
            System.Data.DataSet ds = new System.Data.DataSet();
            string token = "";
            try
            {
                MySql.Data.MySqlClient.MySqlDataAdapter da = new MySql.Data.MySqlClient.MySqlDataAdapter();

                string sql = "SELECT * FROM user where usteamid='" + steamId.ConvertToUInt64().ToString() + "'";
                MySqlCommand cmd = new MySqlCommand(sql, conn);
                da.SelectCommand = cmd;
                conn.Open();
                da.Fill(ds);
                conn.Close();

                if (ds.Tables[0].Rows.Count > 0)
                {
                    string url = ds.Tables[0].Rows[0]["profiletradeurl"].ToString();
                    if (url.IndexOf('=') > 0)
                    {
                        token = url.Substring(url.LastIndexOf('=') + 1);
                    }
                    token = token.Trim();
                }

            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }

            conn.Close();

            return token;
        }

        #endregion

        /// <summary>
        /// Returns id of bot.
        /// </summary>
        /// <returns></returns>
        protected long GetBotId()
        {
            long botId;
            switch (Bot.DisplayName)
            {
                case botName1:
                    botId = botId1;
                    break;
                case botName2:
                    botId = botId2;
                    break;
                case botName3:
                    botId = botId3;
                    break;
                case botName4:
                    botId = botId4;
                    break;
                case botName5:
                    botId = botId5;
                    break;
                default:
                    Log.Error("You added new bots or changed bots name, update each switch");
                    botId = 0;
                    break;
            }

            return botId;
        }
    }
}