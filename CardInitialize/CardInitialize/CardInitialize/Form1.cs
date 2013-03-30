using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using webService;
using System.Reflection;
//for JSON
using System.Web.Script.Serialization;


namespace WindowsFormsApplication1
{
    public partial class Form1 : Form
    {
        int hContext = 0, retcode, hCard, ActiveProtocol;   //hContext -> handle for resource manager
        string rreader;
        WebService agent = new WebService();
        public string sURL = "http://localhost/phptest/web_service/sample2.php/UserStudent/";
        Student stud = new Student();
        int session_stud_id; //session id of user
#region smart card variables
        byte[] array = new byte[256]; 
        byte[] SendBuff = new byte[262];
        byte[] RecvBuff = new byte[262];
        byte[] tmpArray = new byte[56];
        int indx, SendBuffLen, RecvBuffLen, Aprotocol;
        string sTemp;
        ModWinsCard.APDURec apdu = new ModWinsCard.APDURec();
#endregion
        public Form1()
        {
            InitializeComponent();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            //panel1.Hide();
            button4.Enabled = false;
            
        }

        private bool check_reader()
        {

        //SCardEstablishContext
            logBox1.Items.Add("Calling SCardEstablishContext...");
            retcode = ModWinsCard.SCardEstablishContext(ModWinsCard.SCARD_SCOPE_USER, 0, 0, ref hContext);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
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
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
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
            button1.Visible = true;     //Enable SCardConnect Button
            button1.Enabled = true;
            button2.Visible = false;    //Disable search reader Button 
            button2.Enabled = false;
            label1.Text = "Please place contact card on reader and press OK to Initialize.";
            return true;    
        }

        private bool initCard() {
            logBox1.Items.Add("Calling SCardConnect...");
            // Connect to the reader using hContext handle and obtain hCard handle  
            retcode = ModWinsCard.SCardConnect(hContext, comboBox1.SelectedItem.ToString(), ModWinsCard.SCARD_SHARE_EXCLUSIVE, 0 | 1, ref hCard, ref ActiveProtocol);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }
            logBox1.Items.Add("Success!");
            //start putting the data on the card
            try
            {
                    personalization_stage();
                    logBox1.Items.Add("Personalization stage finished.");
                    writeData();
                    logBox1.Items.Add("Student info on card successfully written.");
                    readData();
                    //update database, set user_stud::stud_status to activated
                    StringBuilder str = new StringBuilder();
                    str.Append(sURL);
                    str.Append(session_stud_id.ToString());
                    send_http_request("PUT", str.ToString(), new{string_stud_status = "activated"});
                    MessageBox.Show("Status: " + stud.stud_status);
                    button1.Enabled = false;
                    label6.Enabled = true;
                    
            }
            catch {
                logBox1.Items.Add("There was an error in initializing card");
                return false;
            }
            return true;
        }

        //initialize card
        private void button1_Click(object sender, EventArgs e)
        {
            initCard();
        }

        //try-again button
        private void button2_Click(object sender, EventArgs e)
        {
            check_reader();
        }

        //log-in button
        private void button3_Click(object sender, EventArgs e)
        {
            button4.Enabled = false;            //disable proceed
            string username = textBox1.Text;
            string pwd = textBox2.Text;
            StringBuilder sb = new StringBuilder();
            sb.Append(sURL);
            sb.Append(textBox1.Text);
            try
            {
                send_http_request("GET", sb.ToString(), null);
                if (stud.error == "true") logBox1.Items.Add(stud.error_message);
                else if (stud.usrname == username && stud.pwd == pwd)
                {
                   logBox1.Items.Add("Found user");
                   //logBox1.Items.Add(stud.course + "college " + stud.college);
                   logBox1.Items.Add(stud.stud_status);
                   if (stud.stud_status == "approved")
                   {
                       button4.Enabled = true;
                   }
                }
                else logBox1.Items.Add("Username and Password does not match");
            }
            catch
            {
                //MessageBox.Show("There was an error fetching the data");
                logBox1.Items.Add("There was an error fetching the data");
            }
            
        }

        //proceed button
        private void button4_Click(object sender, EventArgs e)
        {
            //set session id
            session_stud_id = stud.id;
            label5.Text = stud.fname;
            panel2.Hide();
            panel1.Show();
            button1.Enabled = false; //disable and hide button1
            button1.Visible = false;
            label1.Text = "NO CARD READER DETECTED";
            check_reader();

        }

        public void send_http_request(string req_method, string args, object obj)
        {
            string data = "";

            if (req_method == "GET")
                data = agent.HttpGet(args);
            else if (req_method == "POST") data = agent.HttpPost(args, "dummy", "dummy");
            else if (req_method == "PUT") data = agent.HttpPut(args, obj);
                //MessageBox.Show(data);
                JavaScriptSerializer jSerialize = new JavaScriptSerializer();
                stud = jSerialize.Deserialize<Student>(data);                   //initialize student
                //string sb = "center = " + stud.ToString();
               // MessageBox.Show(stud);
                //logBox1.Items.Add(sb);
        }

        #region smartcardfunctions

        private bool personalization_stage()
        {

            SubmitIC();
            SelectFile(0xFF, 0x2);

            /*  Write to FF 02
            '    This will create 6 User files, no Option registers and
            '    Security Option registers defined, Personalization bit
            '    is not set */

            tmpArray[0] = 0x0;      // 00    Option registers
            tmpArray[1] = 0x0;      // 00    Security option register
            tmpArray[2] = 0x6;      // 7    No of user files
            tmpArray[3] = 0x0;      // 00    Personalization bit

            writeRecord(0x00, 0x00, 0x04, 0x04, ref tmpArray);

            logBox1.Items.Add("FF 02 (Personalization file) is updated");

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }
            else
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                logBox1.Items.Add("Card is successful.");
            }

            // Select FF 04
            logBox1.Items.Add("Select FF 04");

            SelectFile(0xFF, 0x04);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            // Send IC Code
            SubmitIC();
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            // Write to FF 04
            //  Write to first record of FF 04, personal information
            tmpArray[0] = 0x32;       // 50     Record length
            tmpArray[1] = 0x9;       // 9     No of records (name, studno, college, course, username, password, id, cnum, email)
            tmpArray[2] = 0x00;       // 00    Read security attribute
            tmpArray[3] = 0x00;       // 00    Write security attribute
            tmpArray[4] = 0xAA;       // AA    File identifier
            tmpArray[5] = 0x11;       // 11    File identifier

            writeRecord(0x00, 0x00, 0x06, 0x06, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File AA 11 is defined");

            //  Write to second record of FF 04, 1st ticket object
            tmpArray[0] = 0x32;       // 50    Record length
            tmpArray[1] = 0x07;       // 2     No of records (id,title,venue,sched,status,flag,ticket_class)
            tmpArray[2] = 0x00;       // 00    Read security attribute
            tmpArray[3] = 0x00;       // 00    Write security attribute
            tmpArray[4] = 0xBB;       // BB    File identifier
            tmpArray[5] = 0x22;       // 22    File identifier

            writeRecord(0x00, 0x01, 0x06, 0x06, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File BB 22 is defined");

            //  Write to third record of FF 04, 2nd ticket object
            tmpArray[0] = 0x32;       // 50     Record length
            tmpArray[1] = 0x07;       // 5     No of records
            tmpArray[2] = 0x00;       // 00    Read security attribute
            tmpArray[3] = 0x00;       // 00    Write security attribute
            tmpArray[4] = 0xCC;       // CC    File identifier
            tmpArray[5] = 0x33;       // 33    File identifier

            writeRecord(0x00, 0x02, 0x06, 0x06, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File CC 33 is defined");

            //  Write to fourth record of FF 04, 3rd ticket object
            tmpArray[0] = 0x32;       // 50     Record length
            tmpArray[1] = 0x07;       // 5     No of records
            tmpArray[2] = 0x00;       // 00    Read security attribute
            tmpArray[3] = 0x00;       // 00    Write security attribute
            tmpArray[4] = 0xDD;       // DD    File identifier
            tmpArray[5] = 0x44;       // 44    File identifier

            writeRecord(0x00, 0x03, 0x06, 0x06, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File DD 44 is defined");


            //  Write to 5th record of FF 04, 4th ticket object
            tmpArray[0] = 0x32;       // 50     Record length
            tmpArray[1] = 0x07;       // 5     No of records
            tmpArray[2] = 0x00;       // 00    Read security attribute
            tmpArray[3] = 0x00;       // 00    Write security attribute
            tmpArray[4] = 0xEE;       // EE    File identifier
            tmpArray[5] = 0x55;       // 55    File identifier

            writeRecord(0x00, 0x04, 0x06, 0x06, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File EE 55 is defined");


            //  Write to 6th record of FF 04, 5th ticket object
            tmpArray[0] = 0x32;       // 50     Record length
            tmpArray[1] = 0x07;       // 5     No of records
            tmpArray[2] = 0x00;       // 00    Read security attribute
            tmpArray[3] = 0x00;       // 00    Write security attribute
            tmpArray[4] = 0xE1;       // E1    File identifier
            tmpArray[5] = 0x66;       // 66    File identifier

            writeRecord(0x00, 0x05, 0x06, 0x06, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File E1 66 is defined");


            //  Select 3 User Files created previously for validation
            // Select User File AA 11
            SelectFile(0xAA, 0x11);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File AA 11 is selected");

            //  Select User File BB 22
            SelectFile(0xBB, 0x22);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File BB 22 is selected");

            //  Select User File CC 33
            SelectFile(0xCC, 0x33);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File CC 33 is selected");
            
            //  Select User File DD 44
            SelectFile(0xDD, 0x44);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File DD 44 is selected");

            //  Select User File EE 55
            SelectFile(0xEE, 0x55);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File EE 55 is selected");

            //  Select User File FF 66
            SelectFile(0xE1, 0x66);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("User File E1 66 is selected");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

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

            //logBox1.Items.Add("Select FF 02");

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

            logBox1.Items.Add("Write to FF 02");
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

        #endregion


        #region write data

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

        private void clearBuffer() {
            for (indx = 0; indx < tmpArray.Length; indx++)
                tmpArray[indx] = (byte)Asc(" ");
        }

        //write data from database to card IC
        public bool writeData() {

            string sdata;

            //write personal information on AA 11
            SelectFile(0xAA, 0x11);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = stud.id.ToString();
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

            logBox1.Items.Add("User Id Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1; 


            sdata = "";
            sdata = stud.fname + " " + stud.mname + " " + stud.lname + " " + stud.suffix;
            clearBuffer();

            //Student name
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x01, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Name Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.usrname;
            clearBuffer();

            //Username
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x02, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Username Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.pwd;
            clearBuffer();

            //Password
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x03, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Password Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.stud_no;
            clearBuffer();

            //Student number
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x04, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Student number Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.college;
            clearBuffer();

            //college
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x05, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("College Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.course;
            clearBuffer();

            //Course
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x06, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Course Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.cnum;
            clearBuffer();

            //Contact number
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x07, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Contact number Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            sdata = "";
            sdata = stud.email;
            clearBuffer();

            //Email
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x08, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Email Written.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1; 

            //initialize ticket file -> flag -> allocated

            //write ticket on BB 22
            SelectFile(0xBB, 0x22);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = "unallocated";
            clearBuffer();

            //flag
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket object 1 unallocated.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //write ticket on CC 33
            SelectFile(0xCC, 0x33);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = "unallocated";
            clearBuffer();

            //flag
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket object 2 unallocated.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //write ticket on DD 44
            SelectFile(0xDD, 0x44);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = "unallocated";
            clearBuffer();

            //flag
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket object 3 unallocated.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //write ticket on EE 55
            SelectFile(0xEE, 0x55);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = "unallocated";
            clearBuffer();

            //flag
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket object 4 unallocated.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1;

            //write ticket on E1 66
            SelectFile(0xE1, 0x66);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            sdata = "";
            sdata = "unallocated";
            clearBuffer();

            //flag
            for (indx = 0; indx < sdata.Length; indx++)
                tmpArray[indx] = (byte)Asc(sdata.Substring(indx, 1));

            writeRecord(0x01, 0x00, 0x32, 0x32, ref tmpArray);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return false;
            }

            logBox1.Items.Add("Ticket object 5 unallocated.");
            logBox1.SelectedIndex = logBox1.Items.Count - 1; 
            return true;
        }
        public void readData() {

            SelectFile(0xAA, 0x11);

            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(ModWinsCard.GetScardErrMsg(retcode));
                return;
            }

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
        }
        #endregion


    }
}
