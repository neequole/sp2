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

        public Form1()
        {
            InitializeComponent();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            groupBox1.Hide();
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
            return true;
        }

        private void button1_Click(object sender, EventArgs e)
        {
            initCard();
        }

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
                send_http_request("GET", sb.ToString());
                if (stud.usrname == username && stud.pwd == pwd)
                {
                   logBox1.Items.Add("Found user");
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

        public void send_http_request(string req_method, string args)
        {
            string data = "";

                if (req_method == "GET")
                    data = agent.HttpGet(args);
                else data = agent.HttpPost(args, "dummy", "dummy");
                JavaScriptSerializer jSerialize = new JavaScriptSerializer();
                stud = jSerialize.Deserialize<Student>(data);                   //initialize student
                //string sb = "center = " + stud.ToString();
                //MessageBox.Show(sb);
                //logBox1.Items.Add(sb);

           

      
        }

        private void button4_Click(object sender, EventArgs e)
        {
            //set session id
            session_stud_id = stud.id;
            label5.Text = stud.fname;
            groupBox1.Show();
            groupBox2.Hide();
            button1.Enabled = false; //disable and hide button1
            button1.Visible = false;
            label1.Text = "NO CARD READER DETECTED";
            check_reader();
            
        }
        
 
        
    }
}
