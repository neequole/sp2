using System;
using System.Collections;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
//for HTTP REQUEST
using System.Net;           
using System.IO;
//for JSON
using System.Web.Script.Serialization;
//for Web Service
using webService;
//for Regex
using System.Text.RegularExpressions;
//for transaction
using System.Transactions;


namespace WindowsFormsApplication1
{
    public partial class Form1 : Form
    {
        int hContext = 0, retcode, hCard, ActiveProtocol;   //hContext -> handle for resource manager
        /*for web client*/
        WebService agent = new WebService();
        public string UserAgent = @"Mozilla/5.0 (Windows; Windows NT 6.1) AppleWebKit/534.23 (KHTML, like Gecko) Chrome/11.0.686.3 Safari/534.23";
        string sURL = "http://localhost/phptest/web_service/sample2.php/UserAdmin/";
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
        Student stud = new Student();
        AdminUser admin = new AdminUser();
        Booking uBook = new Booking();
        string session_stud_id; //session id of user
        string data;
        int rowIndex; //use for determining row to write in card
        List<Booking> pendingBookings = new List<Booking>();
        RadioButton selected = new RadioButton();
        JavaScriptSerializer jser = new JavaScriptSerializer();
        byte[] list1 = { 0xBB, 0xCC, 0xDD, 0xEE, 0xE1 }; //holds high address
        byte[] list2 = { 34, 51, 68, 85, 102 }; //holds low address


        public Form1()
        {
            InitializeComponent();
            panel2.Enabled = false;
        }

        private void Form1_Load(object sender, EventArgs e)
        {

        }


        private void comboBox1_SelectedIndexChanged(object sender, EventArgs e)
        {

        }

        /*Log in button
         */
        private void button1_Click_1(object sender, EventArgs e)
        {
            string username = textBox1.Text;
            string pwd = textBox2.Text;
            string data = "";
            StringBuilder sb = new StringBuilder();
            sb.Append(sURL);           //class is User
            sb.Append(textBox1.Text);
            try
            {
                data = send_http_request("GET", sb.ToString(), null);
                //MessageBox.Show(data);
                JavaScriptSerializer jSerialize = new JavaScriptSerializer();
                admin = jSerialize.Deserialize<AdminUser>(data);                   //initialize student
                if (admin.error == "true") logBox1.Items.Add(admin.error_message);
                else if (admin.usrname == username && admin.pwd == pwd)
                {
                    logBox1.Items.Add("Found user");
                    //enabled reader section
                    label5.Text = admin.fname;
                    panel1.Enabled = false;
                    groupBox1.Enabled = false;
                    panel2.Enabled = true;
                }
                else logBox1.Items.Add("Username and Password does not match");
            }
            catch
            {
                //MessageBox.Show("There was an error fetching the data");
                logBox1.Items.Add("There was an error fetching the data");
            }
        }

        #region webservice
        public string send_http_request(string req_method, string args, object obj)
        {
            string data = "";

            if (req_method == "GET")
                data = agent.HttpGet(args);
            else if (req_method == "POST") data = agent.HttpPost(args, "dummy", "dummy");
            else if (req_method == "PUT") data = agent.HttpPut(args, obj);
            //MessageBox.Show(data);
            return data;
            //string sb = "center = " + stud.ToString();
            // MessageBox.Show(stud);
            //logBox1.Items.Add(sb);
        }

        #endregion

        #region smartcard utils
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

            button2.Enabled = false;
            button3.Enabled = true;
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
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                logBox1.Items.Add("SCardTransmit Error!");
            }
            else
            {
                logBox1.Items.Add("SCardTransmit OK...");
            }

            sTemp = "";
            // do loop for sendbuffLen
            for (indx = 0; indx < SendBuffLen; indx++)
                sTemp = sTemp + " " + string.Format("{0:X2}", SendBuff[indx]);


            // Display Send Buffer Value
            logBox1.Items.Add("Send Buffer : " + sTemp);
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sTemp = "";
            // do loop for RecvbuffLen
            for (indx = 0; indx < RecvBuffLen; indx++)
                sTemp = sTemp + " " + string.Format("{0:X2}", RecvBuff[indx]);

            // Display Receive Buffer Value
            logBox1.Items.Add("Receive Buffer:" + sTemp);
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            if (apdu.IsSend == false)
            {
                for (indx = 0; indx < apdu.bP3 + 2; indx++)
                    apdu.Data[indx] = RecvBuff[indx];

            }
            logBox1.SelectedIndex = logBox1.Items.Count - 1;
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
            logBox1.Items.Add("Submit IC");

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

            logBox1.Items.Add("Select " + HiAddr.ToString() + " " + LoAddr.ToString());

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

        public void readData()
        {

            SelectFile(0xAA, 0x11);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }
            card_info.Items.Add("----------PERSONAL INFORMATION----------");
            //read id
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
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
            
            Regex rgx = new Regex("[^a-zA-Z0-9]");
            string tmpStr2 = rgx.Replace(tmpStr, "");
            //save as session
            //MessageBox.Show(tmpStr2 + " Length: " + tmpStr2.Length);
            session_stud_id = tmpStr2;

            card_info.Items.Add("User ID: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read Name
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

            card_info.Items.Add("Name: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read username
            readRecord(0x02, 0x32);

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

            card_info.Items.Add("Username: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read password
            readRecord(0x03, 0x32);

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

            card_info.Items.Add("Password: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read student number
            readRecord(0x04, 0x32);

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

            card_info.Items.Add("Student No: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read College
            readRecord(0x05, 0x32);

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

            card_info.Items.Add("College: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read course
            readRecord(0x06, 0x32);

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

            card_info.Items.Add("Course: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            card_info.Items.Add("----------END OF PERSONAL INFORMATION----------");
            card_info.Items.Add("");
            card_info.Items.Add("----------CONTACT INFORMATION----------");

            //read cnum
            readRecord(0x07, 0x32);

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

            card_info.Items.Add("Contact number: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //read email
            readRecord(0x08, 0x32);

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

            card_info.Items.Add("Email: " + tmpStr);

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            card_info.Items.Add("----------END OF CONTACT INFORMATION----------");
        }

        public bool write_ticket()
        {
            string sdata;

            //select user file
            SelectFile(HiAddr, LoAddr);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = "allocated";
            clearBuffer();
            //user id
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Flag is written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;


            sdata = "";
            sdata = dataGridView2.Rows[rowIndex].Cells[0].Value.ToString();
            clearBuffer();

            //Book id
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x01, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Book ID Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = dataGridView2.Rows[rowIndex].Cells[1].Value.ToString();
            clearBuffer();

            //Title
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x02, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Title Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = dataGridView2.Rows[rowIndex].Cells[2].Value.ToString();
            clearBuffer();

            //Venue
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x03, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Venue Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = dataGridView2.Rows[rowIndex].Cells[3].Value.ToString();
            clearBuffer();

            //Schedule
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x04, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Schedule Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = "activated"; //from pending -> activated
            clearBuffer();

            //status
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x05, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket Status Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = dataGridView2.Rows[rowIndex].Cells[4].Value.ToString();
            clearBuffer();

            //e_tclass
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x06, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket Class Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;


            return true;
        }


        #endregion


        #region etc
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


        private void fetch_ticket() {

            string temp_book_id = "";
            string temp_title = "";
            string temp_venue = "";
            string temp_sched = "";
            string temp_e_tclass = "";
            string temp_status = "";
            string temp_book_id2 = "";
            string bData;
            string sb3 = "http://localhost/phptest/web_service/sample2.php/Booking/BookingId/";
            JavaScriptSerializer ser2 = new JavaScriptSerializer();
            Booking userBooking = new Booking();
            //select first ticket object
            SelectFile(0xBB, 0x22);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }
            
            //read flag
            readRecord(0x00, 0x32);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
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
                this.dataGridView1.Rows.Add("-", "-", "-", "-", "-", "-");
                radioButton1.Enabled = true;
                //this.dataGridView1.Rows.Insert(0, "one", "two", "three", "four","five");
            }

            else { //read rest of the record
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
                temp_book_id = tmpStr;
                temp_book_id2 = Regex.Replace(tmpStr, "[^.0-9]", ""); 
                //MessageBox.Show(temp_book_id.Length.ToString());
                //read title
                readRecord(0x02, 0x32);

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
                temp_title = tmpStr;

                //read venue
                readRecord(0x03, 0x32);

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
                temp_venue = tmpStr;

                //read sched
                readRecord(0x04, 0x32);

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
                temp_sched = tmpStr;
                
                //read status
                /*
                readRecord(0x05, 0x32);

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
                temp_status = tmpStr;
                */

                //try to get status in web service
                try
                {
                    bData = send_http_request("GET", sb3 + temp_book_id2, null);
                    userBooking = ser2.Deserialize<Booking>(bData);
                    temp_status = userBooking.status;
                }
                catch {
                    temp_status = "Unavailable";
                }


                //read ticket class
                readRecord(0x06, 0x32);

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
                temp_e_tclass = tmpStr;

                this.dataGridView1.Rows.Add(temp_book_id, temp_title, temp_venue, temp_sched, temp_e_tclass, temp_status);
                radioButton1.Enabled = false;
            }

            //select second ticket object
            SelectFile(0xCC, 0x33);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flat
            readRecord(0x00, 0x32);

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
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {
                this.dataGridView1.Rows.Add("-", "-", "-", "-", "-", "-");
                radioButton2.Enabled = true;
                //this.dataGridView1.Rows.Insert(0, "one", "two", "three", "four","five");
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
                temp_book_id = tmpStr;
                temp_book_id2 = Regex.Replace(tmpStr, "[^.0-9]", "");
                //read title
                readRecord(0x02, 0x32);

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
                temp_title = tmpStr;

                //read venue
                readRecord(0x03, 0x32);

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
                temp_venue = tmpStr;

                //read sched
                readRecord(0x04, 0x32);

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
                temp_sched = tmpStr;

                //read status
                /*
                readRecord(0x05, 0x32);

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
                temp_status = tmpStr;
                */
                //try to get status in web service
                try
                {
                    bData = send_http_request("GET", sb3 + temp_book_id2, null);
                    userBooking = ser2.Deserialize<Booking>(bData);
                    temp_status = userBooking.status;
                }
                catch
                {
                    temp_status = "Unavailable";
                }

                //read ticket class
                readRecord(0x06, 0x32);

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
                temp_e_tclass = tmpStr;

                this.dataGridView1.Rows.Add(temp_book_id, temp_title, temp_venue, temp_sched, temp_e_tclass, temp_status);
                
                radioButton2.Enabled = false;
            }

            //select third ticket object
            SelectFile(0xDD, 0x44);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flat
            readRecord(0x00, 0x32);

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
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {
                this.dataGridView1.Rows.Add("-", "-", "-", "-", "-", "-");
                radioButton3.Enabled = true;
                //this.dataGridView1.Rows.Insert(0, "one", "two", "three", "four","five");
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
                temp_book_id = tmpStr;
                temp_book_id2 = Regex.Replace(tmpStr, "[^.0-9]", ""); 
                //read title
                readRecord(0x02, 0x32);

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
                temp_title = tmpStr;

                //read venue
                readRecord(0x03, 0x32);

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
                temp_venue = tmpStr;

                //read sched
                readRecord(0x04, 0x32);

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
                temp_sched = tmpStr;

                //read status
                /*
                readRecord(0x05, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox13.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
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
                temp_status = tmpStr;
                */
                try
                {
                    bData = send_http_request("GET", sb3 + temp_book_id2, null);
                    userBooking = ser2.Deserialize<Booking>(bData);
                    temp_status = userBooking.status;
                }
                catch
                {
                    temp_status = "Unavailable";
                }

                //read ticket class
                readRecord(0x06, 0x32);

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
                temp_e_tclass = tmpStr;

                this.dataGridView1.Rows.Add(temp_book_id, temp_title, temp_venue, temp_sched, temp_e_tclass, temp_status);
                
                radioButton3.Enabled = false;
            }

            //select 4th ticket object
            SelectFile(0xEE, 0x55);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flat
            readRecord(0x00, 0x32);

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
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {
                this.dataGridView1.Rows.Add("-", "-", "-", "-", "-", "-");
                radioButton4.Enabled = true;
                //this.dataGridView1.Rows.Insert(0, "one", "two", "three", "four","five");
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
                temp_book_id = tmpStr;
                temp_book_id2 = Regex.Replace(tmpStr, "[^.0-9]", ""); 

                //read title
                readRecord(0x02, 0x32);

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
                temp_title = tmpStr;

                //read venue
                readRecord(0x03, 0x32);

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
                temp_venue = tmpStr;

                //read sched
                readRecord(0x04, 0x32);

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
                temp_sched = tmpStr;

                //read status
                /*
                readRecord(0x05, 0x32);

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
                temp_status = tmpStr;
                */
                //try to get status in web service
                try
                {
                    bData = send_http_request("GET", sb3 + temp_book_id2, null);
                    userBooking = ser2.Deserialize<Booking>(bData);
                    temp_status = userBooking.status;
                }
                catch
                {
                    temp_status = "Unavailable";
                }

                //read ticket class
                readRecord(0x06, 0x32);

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
                temp_e_tclass = tmpStr;

                this.dataGridView1.Rows.Add(temp_book_id, temp_title, temp_venue, temp_sched, temp_e_tclass, temp_status);
                
                radioButton4.Enabled = false;
            }

            //select 5th ticket object
            SelectFile(0xE1, 0x66);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

            //read flat
            readRecord(0x00, 0x32);

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
            tmpStr = tmpStr.Substring(0, 11);    //remove trailing characters since size is 50
            if (tmpStr == "unallocated")
            {
                this.dataGridView1.Rows.Add("-", "-", "-", "-", "-", "-");
                radioButton5.Enabled = true;
                //this.dataGridView1.Rows.Insert(0, "one", "two", "three", "four","five");
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
                temp_book_id = tmpStr;
                temp_book_id2 = Regex.Replace(tmpStr, "[^.0-9]", ""); 
                //read title
                readRecord(0x02, 0x32);

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
                temp_title = tmpStr;

                //read venue
                readRecord(0x03, 0x32);

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
                temp_venue = tmpStr;

                //read sched
                readRecord(0x04, 0x32);

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
                temp_sched = tmpStr;

                //read status
                /*
                readRecord(0x05, 0x32);

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
                temp_status = tmpStr;
                */
                //try to get status in web service
                try
                {
                    bData = send_http_request("GET", sb3 + temp_book_id2, null);
                    userBooking = ser2.Deserialize<Booking>(bData);
                    temp_status = userBooking.status;
                }
                catch
                {
                    temp_status = "Unavailable";
                }
                //read ticket class
                readRecord(0x06, 0x32);

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
                temp_e_tclass = tmpStr;

                this.dataGridView1.Rows.Add(temp_book_id, temp_title, temp_venue, temp_sched, temp_e_tclass, temp_status);
                
                radioButton5.Enabled = false;
            }

            logBox1.Items.Add("Data read from card is displayed");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            DataGridViewButtonColumn btn2 = new DataGridViewButtonColumn();
            dataGridView1.Columns.Add(btn2);
            btn2.HeaderText = "Action";
            btn2.Text = "Delete";
            btn2.Name = "btn2";
            btn2.FlatStyle = FlatStyle.Popup;
            btn2.UseColumnTextForButtonValue = true;
            edit_deleteButton();

        }

        public bool delete_ticket(string bookingId) { 
        

            int i = 0;
            string tmpStr, temp_book_id, sdata;
            //loop through the ticket files
            for (i = 0; i < 5; i++)
            {
                //select first ticket object
                SelectFile(list1[i], list2[i]);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return false;
                }

                //read flag
                readRecord(0x00, 0x32);

                if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                {
                    logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                    return false;
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
                { 
                    //read rest of the record
                    //read book id
                    readRecord(0x01, 0x32);

                    if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                    {
                        logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                        return false;
                    }

                    // Display data read from card to textbox
                    tmpStr = "";
                    indx = 0;

                    while (RecvBuff[indx] != 0x00)
                    {

                        tmpStr = tmpStr + Chr(RecvBuff[indx]);
                        indx = indx + 1;
                    }
                    temp_book_id = Regex.Replace(tmpStr, "[^.0-9]", "");
                    
                    if (temp_book_id == bookingId) {
                        //set to unallocated
                        sdata = "";
                        sdata = "unallocated";
                        clearBuffer();
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add("4" + ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }
                 
                        logBox1.Items.Add("Flag set to unallocated.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;


                        sdata = "";
                        clearBuffer();

                        //Book id
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x01, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }

                        logBox1.Items.Add("Book ID deleted.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                        clearBuffer();

                        //Title
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x02, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }

                        logBox1.Items.Add("Title deleted.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                        clearBuffer();

                        //Venue
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x03, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }

                        logBox1.Items.Add("Venue deleted.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                        clearBuffer();

                        //Schedule
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x04, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }

                        logBox1.Items.Add("Schedule deleted.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                        clearBuffer();

                        //status
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x05, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }

                        logBox1.Items.Add("Ticket Status deleted.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                        clearBuffer();

                        //e_tclass
                        for (indx = 0; indx < sdata.Length; indx++)
                            tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

                        writeRecord(0x01, 0x06, 0x32, 0x32, ref tmpArray);

                        if (retcode != ModWinsCard.SCARD_S_SUCCESS)
                        {
                            logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                            return false;
                        }

                        logBox1.Items.Add("Ticket Class deleted.");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;

                        logBox1.Items.Add("Successfully deleted ticket");
                        if (list1[i] == 0xBB && list2[i] == 22) radioButton1.Enabled = true;
                        else if (list1[i] == 0xCC && list2[i] == 33) radioButton1.Enabled = true;
                        else if (list1[i] == 0xDD && list2[i] == 44) radioButton1.Enabled = true;
                        else if (list1[i] == 0xEE && list2[i] == 55) radioButton1.Enabled = true;
                        else if (list1[i] == 0xE1 && list2[i] == 66) radioButton1.Enabled = true;
                        return true;
                     
                    }
                    
                }
                
            }
            return false;
        }


        public void populate_pending() {
            StringBuilder sb2 = new StringBuilder();
            sb2.Append("http://localhost/phptest/web_service/sample2.php/Booking/UserId/");           //class is booking
            sb2.Append(session_stud_id);                                                     //append current stud id
            sb2.Append("/pending");
            data = send_http_request("GET", sb2.ToString(), null);
            //MessageBox.Show(data);
            JavaScriptSerializer ser = new JavaScriptSerializer();
            pendingBookings = ser.Deserialize<List<Booking>>(data);
            foreach (Booking o in pendingBookings) // Loop through List with foreach
            {
                this.dataGridView2.Rows.Add(o.book_id, o.title, o.venue_name, o.e_date + " " + o.e_stime + " - " + o.e_etime, o.e_tclass,o.status);
            }
            DataGridViewButtonColumn btn = new DataGridViewButtonColumn();
            dataGridView2.Columns.Add(btn);
            btn.HeaderText = "Action";
            btn.Text = "Activate";
            btn.Name = "btn";
            btn.FlatStyle = FlatStyle.Popup;
            btn.UseColumnTextForButtonValue = true;
            edit_activateButton();
        }
        #endregion

        private void edit_activateButton() {
            foreach (DataGridViewRow row in dataGridView2.Rows)
            {
                DataGridViewButtonCell btn = row.Cells[6] as DataGridViewButtonCell;
                if (row.Cells[5].Value.ToString() != "pending")
                {
                    btn.Style.ForeColor = Color.LightGray;
                }
                else {
                    btn.Style.BackColor = Color.LightSalmon;
                    btn.Style.ForeColor = Color.Red;
                }
            }
        }

        private void edit_deleteButton()
        {
            foreach (DataGridViewRow row in dataGridView1.Rows)
            {
                DataGridViewButtonCell btn = row.Cells[6] as DataGridViewButtonCell;
                if (row.Cells[5].Value.ToString() == "" || row.Cells[5].Value.ToString() == "-")
                {
                    btn.Style.ForeColor = Color.LightGray;
                }
                else
                {
                    btn.Style.BackColor = Color.LightSalmon;
                    btn.Style.ForeColor = Color.Red;
                }
            }
        }

        private void button2_Click_1(object sender, EventArgs e)
        {
            check_reader();
        }

        private void button3_Click(object sender, EventArgs e)
        {
            //populate user information
            try
            {
                if (checkCard())
                {
                    readData();
                    logBox1.Items.Add("Information successfully read!");
                    logBox1.SelectedIndex = logBox1.Items.Count - 1;
                    //populate e-ticket
                    try
                    {
                        fetch_ticket();
                        logBox1.Items.Add("Ticket fetch!");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                    }
                    catch
                    {
                        logBox1.Items.Add("Unable to fetch e-tickets");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                    }
                    //populate pending ticket
                    try
                    {
                        populate_pending();
                    }
                    catch
                    {
                        logBox1.Items.Add("Unable to fetch pending bookings");
                        logBox1.SelectedIndex = logBox1.Items.Count - 1;
                    }
                }
            }
            catch {
                card_info.Items.Add("There were no available user information");
            }
        }

        //if e-ticket is to be activated
        private void dataGridView2_CellClick(object sender, DataGridViewCellEventArgs e)
        {
            string putUrl = "http://localhost/phptest/web_service/sample2.php/Booking/BookingId/";
            string data;
            //DateTime today = DateTime.Today;
            //MessageBox.Show(today.ToString());
            if (e.ColumnIndex == 6 && e.RowIndex>=0)
            {   //button column 
                //MessageBox.Show(dataGridView2.Rows[e.RowIndex].Cells[0].Value.ToString());
                //MessageBox.Show("Are you sure?");
                //MessageBox.Show(e.RowIndex.ToString());
                if (dataGridView2.Rows[e.RowIndex].Cells[5].Value.ToString() != "pending") return;
                rowIndex = e.RowIndex;
                //check if a user file is selected
                if (check_radioButton())
                {
                    try
                    {
                        write_ticket();
                        logBox1.Items.Add("Ticket successfully written on card.");
                        selected.Enabled = false;
                        putUrl = putUrl + dataGridView2.Rows[rowIndex].Cells[0].Value.ToString();
                        data = send_http_request("PUT", putUrl, new { string_booking_status = "activated" });  //send booking id
                        dataGridView1.Rows.Clear();
                        dataGridView1.Columns.RemoveAt(6);
                        fetch_ticket();
                        
                        //MessageBox.Show(data);
                        dataGridView2.Columns.RemoveAt(6);
                        dataGridView2.Rows.Clear();
                        populate_pending();

                    }
                    catch {
                        logBox1.Items.Add("Fail to write ticket on card.");
                    }

                    //disable radio button

                    //refresh Grid

                    //make status as activated in database
                }
                else MessageBox.Show("Select User File!");
               
            }
        }


        /*return true when one radio button is checked else false*/
        public bool check_radioButton() {
            if (radioButton1.Enabled == true && radioButton1.Checked) {
                HiAddr = 0xBB;
                LoAddr = 0x22;
                selected = radioButton1;
                return true;
            }
            else if (radioButton2.Enabled == true && radioButton2.Checked) {
                HiAddr = 0xCC;
                LoAddr = 0x33;
                selected = radioButton2;
                return true;
            }
            else if(radioButton3.Enabled == true && radioButton3.Checked) {
                HiAddr = 0xDD;
                LoAddr = 0x44;
                selected = radioButton3;
                return true;
            }
            else if(radioButton4.Enabled == true && radioButton4.Checked) {
                HiAddr = 0xEE;
                LoAddr = 0x55;
                selected = radioButton4;
                return true;
            }
            else if(radioButton5.Enabled == true && radioButton5.Checked) {
                HiAddr = 0xE1;
                LoAddr = 0x66;
                selected = radioButton5;
                return true;
            }
            else return false;
        }

        private void button4_Click(object sender, EventArgs e)
        {
            dataGridView2.Columns.RemoveAt(6);
            dataGridView2.Rows.Clear();
            populate_pending();
        }

        private void dataGridView1_CellContentClick(object sender, DataGridViewCellEventArgs e)
        {
            string putUrl = "http://localhost/phptest/web_service/sample2.php/Booking/BookingId/";
            string data;
            string id;
            //DateTime today = DateTime.Today;
            //MessageBox.Show(today.ToString());
            if (e.ColumnIndex == 6 && e.RowIndex >= 0)
            {
                if (dataGridView1.Rows[e.RowIndex].Cells[5].Value.ToString() == "" || dataGridView1.Rows[e.RowIndex].Cells[5].Value.ToString() == "-") return;
                else{
                //delete booking on card and on database
                    try
                    {
       
                       id = Regex.Replace(dataGridView1.Rows[e.RowIndex].Cells[0].Value.ToString(), "[^.0-9]", "");
                       data = send_http_request("PUT", "http://localhost/phptest/web_service/sample2.php/Booking/BookingId/delete/" + id, new { string_delete = "delete" });
                       //MessageBox.Show(data);
                        uBook = jser.Deserialize<Booking>(data);
                     
                            if (uBook.error == "true")
                            {
                                MessageBox.Show("Fail to delete booking1");
                            }
                            else
                            {
                                //delete ticket on card, update radio button
                                if (delete_ticket(id))
                                {
                                    MessageBox.Show("Ticket Deleted!");
                                    dataGridView1.Rows.Clear();
                                    dataGridView1.Columns.RemoveAt(6);
                                    fetch_ticket();
                                    
                                    //MessageBox.Show(data);
                                }
                                else MessageBox.Show("Fail to delete booking on card!");
                            }
                    }
                    catch
                    {
                        logBox1.Items.Add("Fail to delete booking!");
                    }
                }
            }
           
        }



    }

}


