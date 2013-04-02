using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using webService;
//for JSON
using System.Web.Script.Serialization;
//thread
using System.Threading;
using System.Collections;
//for Regex
using System.Text.RegularExpressions;

namespace facilitatorPortal
{
    
    public partial class Form1 : Form
    {
        int hContext = 0, retcode, hCard, ActiveProtocol;   //hContext -> handle for resource manager
        /*for web client*/
        WebService agent = new WebService();
        public string UserAgent = @"Mozilla/5.0 (Windows; Windows NT 6.1) AppleWebKit/534.23 (KHTML, like Gecko) Chrome/11.0.686.3 Safari/534.23";
        string sURL = "http://localhost/phptest/web_service/sample2.php/Event/";
        /*end of elements for web client*/
        /*for smart card*/
        byte[] array = new byte[256];
        byte[] SendBuff = new byte[262];
        byte[] RecvBuff = new byte[262];
        byte[] tmpArray = new byte[56];
        byte HiAddr, LoAddr;
        int indx, SendBuffLen, RecvBuffLen, Aprotocol;
        string sTemp;
        ModWinsCard.APDURec apdu = new ModWinsCard.APDURec();
        /*end of elements for smart card*/
        List<Event> allEvent = new List<Event>();
        List<Booking2> allBooking = new List<Booking2>();
        string event_sched_id;
        private BackgroundWorker m_oWorker;
        string rreader;
        ArrayList eTicket = new ArrayList();
        JavaScriptSerializer jser = new JavaScriptSerializer();

        public Form1()
        {
            InitializeComponent();
            // Create a background worker thread that ReportsProgress &
            // SupportsCancellation
            // Hook up the appropriate events.
            m_oWorker = new BackgroundWorker();
            m_oWorker.DoWork += new DoWorkEventHandler(m_oWorker_DoWork);
            m_oWorker.ProgressChanged += new ProgressChangedEventHandler
                    (m_oWorker_ProgressChanged);
            m_oWorker.RunWorkerCompleted += new RunWorkerCompletedEventHandler
                    (m_oWorker_RunWorkerCompleted);
            m_oWorker.WorkerReportsProgress = true;
            m_oWorker.WorkerSupportsCancellation = true;
            radioButton1.Enabled = false;
            radioButton2.Enabled = false;
        }

        private void button2_Click(object sender, EventArgs e)
        {
            check_reader();
        }

        private void generate_events() {
            string data;

            data = send_http_request("GET", sURL, null);
            JavaScriptSerializer ser = new JavaScriptSerializer();
            allEvent = ser.Deserialize<List<Event>>(data);
            foreach (Event o in allEvent) // Loop through List with foreach
            {
                this.dataGridView1.Rows.Add(o.id,o.title, o.venue_name, o.e_date, o.e_stime, o.e_etime, o.e_sched_id);
            }
            DataGridViewButtonColumn btn = new DataGridViewButtonColumn();
            dataGridView1.Columns.Add(btn);
            btn.HeaderText = "Action";
            btn.Text = "Select";
            btn.Name = "Select";
            btn.UseColumnTextForButtonValue = true;
        }

        #region webservice
        public string send_http_request(string req_method, string args, object obj)
        {
            string data = "";

            if (req_method == "GET")
                data = agent.HttpGet(args);
            else if (req_method == "POST") data = agent.HttpPost(args, "dummy", "dummy");
            else if (req_method == "PUT") data = agent.HttpPut(args, obj);
            return data;
        }

        #endregion

        #region smartcard
        private bool check_reader()
        {

            //SCardEstablishContext
            logBox1.Items.Add("Calling SCardEstablishContext...");
            retcode = ModWinsCard.SCardEstablishContext(ModWinsCard.SCARD_SCOPE_USER, 0, 0, ref hContext);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(retcode + ": " + ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }
            logBox1.Items.Add("Success!");

            //SCardListReaders
            logBox1.Items.Add("Calling SCardListReaders...");
            int pcchReaders = 0;

            // List PCSC card readers installed 
            retcode = ModWinsCard.SCardListReaders(hContext, null, null, ref pcchReaders);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(retcode + ": " + ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            byte[] ReaderList = new byte[pcchReaders];
            byte[] sReaderGroup = new byte[0];

            //Get the list of reader present again but this time add sReaderGroup, retData as 2rd & 3rd parameter respectively.
            retcode = ModWinsCard.SCardListReaders(hContext, sReaderGroup, ReaderList, ref pcchReaders);
            logBox1.Items.Add("Success!");

            //used to split null delimited strings into string arrays
            char[] delimiter = new char[1];
            delimiter[0] = Convert.ToChar(0);

            //Convert retData(Hexadecimal) value to String 
            string readerStr = System.Text.ASCIIEncoding.ASCII.GetString(ReaderList);
            string[] pcReaders = readerStr.Split(delimiter);   //list of installed readers

            foreach (string readerName in pcReaders)
            {
                if (readerName != null && readerName.Length > 1)
                {
                    logBox1.Items.Add("Found: " + readerName);
                    comboBox1.Items.Add(readerName);
                }
            }

            if (comboBox1.Items.Count > 0)
                comboBox1.SelectedIndex = 0;

            radioButton1.Enabled = true;
            radioButton2.Enabled = true;
            try
            {
                generate_events();
                logBox1.Items.Add("Events successfully fetched!");
            }
            catch {
                logBox1.Items.Add("Failed to fetch events!");
            }
            return true;

        }

        private bool checkCard()
        {
            logBox1.Items.Add("Calling SCardConnect...");
            // Connect to the reader using hContext handle and obtain hCard handle  
            retcode = ModWinsCard.SCardConnect(hContext, comboBox1.SelectedItem.ToString(), ModWinsCard.SCARD_SHARE_EXCLUSIVE, 0 | 1, ref hCard, ref ActiveProtocol);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(retcode + ": " + ModWinsCard.GetScardErrMsg(retcode));
                logBox1.Items.Add("Please place contact card of user to reader and Press OK.");
                return false;
            }
            logBox1.Items.Add("Success!");
            return true;
        }

        private bool checkCard2()
        {
           // logBox1.Items.Add("Calling SCardConnect...");
            // Connect to the reader using hContext handle and obtain hCard handle  
            retcode = ModWinsCard.SCardConnect(hContext, rreader, ModWinsCard.SCARD_SHARE_EXCLUSIVE, 0 | 1, ref hCard, ref ActiveProtocol);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
           //     logBox1.Items.Add(retcode + ": " + ModWinsCard.GetScardErrMsg(retcode));
            //    logBox1.Items.Add("Please place contact card of user to reader and Press OK.");
                return false;
            }
           // logBox1.Items.Add("Success!");
            return true;
        }

        private void PerformTransmitAPDU(ref ModWinsCard.APDURec apdu)
        {

            ModWinsCard.SCARD_IO_REQUEST SendRequest;
            ModWinsCard.SCARD_IO_REQUEST RecvRequest;

            SendBuff[0] = apdu.bCLA;
            SendBuff[1] = apdu.bINS;
            SendBuff[2] = apdu.bP1;
            SendBuff[3] = apdu.bP2;
            SendBuff[4] = apdu.bP3;

            if (apdu.IsSend)
            {
                for (indx = 0; indx < apdu.bP3; indx++)
                    SendBuff[5 + indx] = apdu.Data[indx];

                SendBuffLen = 5 + apdu.bP3;
                RecvBuffLen = 2;
            }
            else
            {
                SendBuffLen = 5;
                RecvBuffLen = 2 + apdu.bP3;
            }

            SendRequest.dwProtocol = Aprotocol;
            SendRequest.cbPciLength = 8;

            RecvRequest.dwProtocol = Aprotocol;
            RecvRequest.cbPciLength = 8;

            retcode = ModWinsCard.SCardTransmit(hCard, ref SendRequest, ref SendBuff[0], SendBuffLen, ref SendRequest, ref RecvBuff[0], ref RecvBuffLen);


            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
               // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
               // logBox1.Items.Add("SCardTransmit Error!");
            }
            else
            {
               // logBox1.Items.Add("SCardTransmit OK...");
            }

            sTemp = "";
            // do loop for sendbuffLen
            for (indx = 0; indx < SendBuffLen; indx++)
                sTemp = sTemp + " " + string.Format("{0:X2}", SendBuff[indx]);


            // Display Send Buffer Value
            //logBox1.Items.Add("Send Buffer : " + sTemp);
           // logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sTemp = "";
            // do loop for RecvbuffLen
            for (indx = 0; indx < RecvBuffLen; indx++)
                sTemp = sTemp + " " + string.Format("{0:X2}", RecvBuff[indx]);

            // Display Receive Buffer Value
            //logBox1.Items.Add("Receive Buffer:" + sTemp);
            //logBox1.SelectedIndex = logBox1.Items.Count - 1;

            if (apdu.IsSend == false)
            {
                for (indx = 0; indx < apdu.bP3 + 2; indx++)
                    apdu.Data[indx] = RecvBuff[indx];

            }
            //logBox1.SelectedIndex = logBox1.Items.Count - 1;
            //logBox1.Items.Add("apdu!!");
        }

        private void SubmitIC()
        {

            //Send IC Code
            apdu.Data = array;

            apdu.bCLA = 0x80;          // CLA
            apdu.bINS = 0x20;          // INS
            apdu.bP1 = 0x07;           // P1
            apdu.bP2 = 0x00;           // P2
            apdu.bP3 = 0x08;           // P3

            apdu.Data[0] = 0x41;       // A
            apdu.Data[1] = 0x43;       // C
            apdu.Data[2] = 0x4F;       // O
            apdu.Data[3] = 0x53;       // S
            apdu.Data[4] = 0x54;       // T
            apdu.Data[5] = 0x45;       // E
            apdu.Data[6] = 0x53;       // S
            apdu.Data[7] = 0x54;       // T

            apdu.IsSend = true;
            //logBox1.Items.Add("Submit IC");

            PerformTransmitAPDU(ref apdu);
        }

        private void SelectFile(byte HiAddr, byte LoAddr)
        {

            // Select FF 02
            apdu.Data = array;

            apdu.bCLA = 0x080;       // CLA
            apdu.bINS = 0x0A4;       // INS
            apdu.bP1 = 0x00;         // P1
            apdu.bP2 = 0x00;         // P2
            apdu.bP3 = 0x02;         // P3

            apdu.Data[0] = HiAddr;      // Value of High Byte
            apdu.Data[1] = LoAddr;      // Value of Low Byte

            apdu.IsSend = true;

            //logBox1.Items.Add("Select " + HiAddr.ToString() + " " + LoAddr.ToString());

            PerformTransmitAPDU(ref apdu);
        }

        private void writeRecord(int caseType, byte RecNo, byte maxLen, byte DataLen, ref byte[] ApduIn)
        {
            int i;
            if (caseType == 1)    // If card data is to be erased before writing new data. Re-initialize card values to $00
            {
                //logBox1.Items.Add("hello1");
                apdu.bCLA = 0x80;        // CLA
                apdu.bINS = 0xD2;        // INS
                apdu.bP1 = RecNo;		 // Record No
                apdu.bP2 = 0x00;         // P2
                apdu.bP3 = maxLen;        // Length of Data

                apdu.IsSend = true;
                for (i = 0; i < maxLen; i++)
                    apdu.Data[i] = ApduIn[i];

                //logBox1.Items.Add("hello1.5");
                PerformTransmitAPDU(ref apdu);
                //logBox1.Items.Add("hello2");
            }

            //Write data to card
            apdu.bCLA = 0x80;       // CLA
            apdu.bINS = 0xD2;       // INS
            apdu.bP1 = RecNo;       // Record No
            apdu.bP2 = 0x00;        // P2
            apdu.bP3 = DataLen;     // Length of Data

            apdu.IsSend = true;

            for (i = 0; i < maxLen; i++)
                apdu.Data[i] = ApduIn[i];

            // logBox1.Items.Add("Write to FF 02");
            PerformTransmitAPDU(ref apdu);
            //logBox1.Items.Add("HAHHA2");

        }

        private void readRecord(byte RecNo, byte dataLen)
        {
            apdu.Data = array;

            // Read data from card
            apdu.bCLA = 0x80;        // CLA
            apdu.bINS = 0xB2;         // INS
            apdu.bP1 = RecNo;        // Record No
            apdu.bP2 = 0x00;         // P2
            apdu.bP3 = dataLen;      // Length of Data

            apdu.IsSend = false;

            PerformTransmitAPDU(ref apdu);

        }

        int Asc(string character)
        {
            if (character.Length == 1)
            {
                System.Text.ASCIIEncoding asciiEncoding = new System.Text.ASCIIEncoding();
                int intAsciiCode = (int)asciiEncoding.GetBytes(character)[0];
                return (intAsciiCode);
            }
            else
            {
                throw new Exception("Character is not valid.");
            }

        }

        Char Chr(int i)
        {
            //Return the character of the given character value
            return Convert.ToChar(i);
        }

        private void clearBuffer()
        {
            for (indx = 0; indx < tmpArray.Length; indx++)
                tmpArray[indx] = (byte)Asc(" ");
        }
        #endregion

        private void dataGridView1_CellContentClick(object sender, DataGridViewCellEventArgs e)
        {
            // string putUrl = "http://localhost/phptest/web_service/sample2.php/Booking/BookingId/";
            // string data;
            //DateTime today = DateTime.Today;
            //MessageBox.Show(today.ToString());
            if (e.ColumnIndex == 7 && e.RowIndex >= 0)
            {
                //recolor rows in datagridview
                foreach (DataGridViewRow row in dataGridView1.Rows) {
                    row.DefaultCellStyle.BackColor = Color.White;
                }
                dataGridView1.Rows[e.RowIndex].DefaultCellStyle.BackColor = Color.Yellow;
                textBox1.Text = dataGridView1.Rows[e.RowIndex].Cells[1].Value.ToString() + " " + dataGridView1.Rows[e.RowIndex].Cells[2].Value.ToString() + " " + dataGridView1.Rows[e.RowIndex].Cells[3].Value.ToString() + " " + dataGridView1.Rows[e.RowIndex].Cells[4].Value.ToString() + " " + dataGridView1.Rows[e.RowIndex].Cells[5].Value.ToString();
                event_sched_id = dataGridView1.Rows[e.RowIndex].Cells[6].Value.ToString();
                try
                {
                    populate_bookings();
                    logBox1.Items.Add("Bookings successfully fetched!");
                }
                catch {
                    logBox1.Items.Add("Failed to fetch bookings!");
                }
            }
        }

        private void populate_bookings() {
            string putUrl = "http://localhost/phptest/web_service/sample2.php/Booking/EventSchedId/";
            string data;
            data = send_http_request("GET", putUrl+event_sched_id, null);
            JavaScriptSerializer ser = new JavaScriptSerializer();
            allBooking = ser.Deserialize<List<Booking2>>(data);
            dataGridView2.Rows.Clear();
            foreach (Booking2 o in allBooking) // Loop through List with foreach
            {
                this.dataGridView2.Rows.Add(o.book_id,o.fname+" "+o.mname+" "+o.lname,o.stud_no,o.title,o.venue_name,o.e_date + " " + o.e_stime + " " + o.e_etime, o.status);
            }
        }

        private void button1_Click(object sender, EventArgs e)
        {
            if (check_radioButton() && event_sched_id != null && event_sched_id != "")
            {
                button1.Enabled = false;
                button3.Enabled = true;
                //start detecting cards
                rreader = comboBox1.SelectedItem.ToString();
                m_oWorker.RunWorkerAsync();
            }
            else MessageBox.Show("Select a function and an event to start processing cards.");
        }

       
        private bool check_radioButton() {
            if (radioButton1.Checked || radioButton2.Checked)
            {
                return true;
            }
            else return false;
        }

        private void button3_Click(object sender, EventArgs e)
        {
            if (m_oWorker.IsBusy)
            {
                // Notify the worker thread that a cancel has been requested.

                // The cancel will not actually happen until the thread in the

                // DoWork checks the m_oWorker.CancellationPending flag. 

                m_oWorker.CancelAsync();
            }
            button3.Enabled = false;
            button1.Enabled = true;
        }

        void fetch_bookingId() {
            
            //select first ticket object
            SelectFile(0xBB, 0x22);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
               // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }
            
            //read flag
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
               // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            // Display data read from card to textbox
            string tmpStr = "";
            indx = 0;

            while (RecvBuff[indx] != 0x00)
            {

                tmpStr = tmpStr + Chr(RecvBuff[indx]);
                indx = indx + 1;
            }
            tmpStr = tmpStr.Substring(0,11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {

            }
            else
            { //read rest of the record
                //read book id
                readRecord(0x01, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return;
                }

                // Display data read from card to textbox
                tmpStr = "";
                indx = 0;

                while (RecvBuff[indx] != 0x00)
                {

                    tmpStr = tmpStr + Chr(RecvBuff[indx]);
                    indx = indx + 1;
                }
                eTicket.Add(Regex.Replace(tmpStr, "[^.0-9]", "")); 
            }

            //select 2nd ticket object
            SelectFile(0xCC, 0x33);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flag
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            // Display data read from card to textbox
            tmpStr = "";
            indx = 0;

            while (RecvBuff[indx] != 0x00)
            {

                tmpStr = tmpStr + Chr(RecvBuff[indx]);
                indx = indx + 1;
            }
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {

            }
            else
            { //read rest of the record
                //read book id
                readRecord(0x01, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return;
                }

                // Display data read from card to textbox
                tmpStr = "";
                indx = 0;

                while (RecvBuff[indx] != 0x00)
                {

                    tmpStr = tmpStr + Chr(RecvBuff[indx]);
                    indx = indx + 1;
                }
                eTicket.Add(Regex.Replace(tmpStr, "[^.0-9]", ""));
            }

            //select 3rd ticket object
            SelectFile(0xDD, 0x44);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flag
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            // Display data read from card to textbox
            tmpStr = "";
            indx = 0;

            while (RecvBuff[indx] != 0x00)
            {

                tmpStr = tmpStr + Chr(RecvBuff[indx]);
                indx = indx + 1;
            }
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {

            }
            else
            { //read rest of the record
                //read book id
                readRecord(0x01, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return;
                }

                // Display data read from card to textbox
                tmpStr = "";
                indx = 0;

                while (RecvBuff[indx] != 0x00)
                {

                    tmpStr = tmpStr + Chr(RecvBuff[indx]);
                    indx = indx + 1;
                }
                eTicket.Add(Regex.Replace(tmpStr, "[^.0-9]", ""));
            }
            //select 4th ticket object
            SelectFile(0xEE, 0x55);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flag
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            // Display data read from card to textbox
            tmpStr = "";
            indx = 0;

            while (RecvBuff[indx] != 0x00)
            {

                tmpStr = tmpStr + Chr(RecvBuff[indx]);
                indx = indx + 1;
            }
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {

            }
            else
            { //read rest of the record
                //read book id
                readRecord(0x01, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return;
                }

                // Display data read from card to textbox
                tmpStr = "";
                indx = 0;

                while (RecvBuff[indx] != 0x00)
                {

                    tmpStr = tmpStr + Chr(RecvBuff[indx]);
                    indx = indx + 1;
                }
                eTicket.Add(Regex.Replace(tmpStr, "[^.0-9]", ""));
            }

            //select 5th ticket object
            SelectFile(0xE1, 0x66);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flag
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                // logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            // Display data read from card to textbox
            tmpStr = "";
            indx = 0;

            while (RecvBuff[indx] != 0x00)
            {

                tmpStr = tmpStr + Chr(RecvBuff[indx]);
                indx = indx + 1;
            }
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {

            }
            else
            { //read rest of the record
                //read book id
                readRecord(0x01, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return;
                }

                // Display data read from card to textbox
                tmpStr = "";
                indx = 0;

                while (RecvBuff[indx] != 0x00)
                {

                    tmpStr = tmpStr + Chr(RecvBuff[indx]);
                    indx = indx + 1;
                }
                eTicket.Add(Regex.Replace(tmpStr, "[^.0-9]", ""));
            }
        }

        /// <summary>
        /// On completed do the appropriate task
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        void m_oWorker_RunWorkerCompleted(object sender, RunWorkerCompletedEventArgs e)
        {
            // The background process is complete. We need to inspect
            // our response to see if an error occurred, a cancel was
            // requested or if we completed successfully.  
            if (e.Cancelled)
            {
                logBox1.Items.Add("Task Cancelled");

                // Check to see if an error occurred in the background process.
            }
            else if (e.Error != null)
            {
                logBox1.Items.Add("Error while performing background operation.");
            }
            else
            {
                // Everything completed normally.
                logBox1.Items.Add("Task Completed...");
            }

            //Change the status of the buttons on the UI accordingly
            button1.Enabled = true;
            button3.Enabled = false;
        }

        /// <summary>
        /// Notification is performed here to the progress bar
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        void m_oWorker_ProgressChanged(object sender, ProgressChangedEventArgs e)
        {

            // This function fires on the UI thread so it's safe to edit

            // the UI control directly, no funny business with Control.Invoke :)

            // Update the progressBar with the integer supplied to us from the

            // ReportProgress() function.  

            progressBar1.Value = e.ProgressPercentage;
            logBox1.Items.Add("Processing......" + progressBar1.Value.ToString() + "%");
        }

        /// <summary>
        /// Time consuming operations go here </br>
        /// i.e. Database operations,Reporting
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        void m_oWorker_DoWork(object sender, DoWorkEventArgs e)
        {
            // The sender is the BackgroundWorker object we need it to
            // report progress and check for cancellation.
            //NOTE : Never play with the UI thread here...
            string data;
            bool flag;
            for (int i = 0; i < 1000; i++)
            {
                Thread.Sleep(1000);
                if (checkCard2()) {
                    MessageBox.Show("Card inserted!");
                    flag = false;
                    //fetch booking ids from the card
                    fetch_bookingId();
                    foreach(string b in eTicket){
                        data = send_http_request("GET", "http://localhost/phptest/web_service/sample2.php/Booking/BookSched/" + b + "/" + event_sched_id, null);
                        Booking bb = jser.Deserialize<Booking>(data);
                        if (bb.error == "true")
                        {
                        
                        }
                        else
                        {
                            flag = true;
                            //send http put change to done
                            //data = send_http_request("PUT", "http://localhost/phptest/web_service/sample2.php/Booking/BookingId/" + b, null);
                            //messagebox to show that student gains entry
                            MessageBox.Show("User Accepted!");
                            break;
                        }
                        
                    }
                    if (flag == false) MessageBox.Show("User Rejected!");
                } 
                // Periodically report progress to the main thread so that it can
                // update the UI.  In most cases you'll just need to send an
                // integer that will update a ProgressBar                    
                m_oWorker.ReportProgress(i);
                // Periodically check if a cancellation request is pending.
                // If the user clicks cancel the line
                // m_AsyncWorker.CancelAsync(); if ran above.  This
                // sets the CancellationPending to true.
                // You must check this flag in here and react to it.
                // We react to it by setting e.Cancel to true and leaving
                if (m_oWorker.CancellationPending)
                {
                    // Set the e.Cancel flag so that the WorkerCompleted event
                    // knows that the process was cancelled.
                    e.Cancel = true;
                    m_oWorker.ReportProgress(0);
                    return;
                }
            }

            //Report 100% completion on operation completed
            m_oWorker.ReportProgress(100);
        }

        

        

        
    }
}
