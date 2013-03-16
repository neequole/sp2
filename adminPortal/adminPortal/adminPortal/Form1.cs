using System;
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

namespace WindowsFormsApplication1
{
    public partial class Form1 : Form
    {
        int hContext = 0, retcode, hCard, ActiveProtocol;   //hContext -> handle for resource manager
        /*for web client*/
        public string UserAgent = @"Mozilla/5.0 (Windows; Windows NT 6.1) AppleWebKit/534.23 (KHTML, like Gecko) Chrome/11.0.686.3 Safari/534.23";
        string sURL = "http://localhost/phptest/web_service/sample2.php/UserStudent/31";
        /*end of elements for web client*/

        public Form1()
        {
            InitializeComponent();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
           // bookingPanel1.Hide();
            button1.Enabled = false; //disable and hide button1
            button1.Visible = false;
            msgBox.Items.Insert(0, "NO CARD READER DETECTED");
          //  check_reader();
            send_http_request("GET",sURL);
        }


        /*Web service*/

        /*
         GET METHOD
         @param url = url of the request
         @return 
         */
        public string HttpGet(string url)
        {
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "GET";
            request.UserAgent = UserAgent;
            HttpWebResponse response = (HttpWebResponse)request.GetResponse();
            StreamReader sr = new StreamReader(response.GetResponseStream());
            return sr.ReadToEnd();
        }

        /*
         POST METHOD
         * @param 
         * url = url of the request
         * post = POST data to be passed
         * refer = referrer of the request
         * @return
        */
        public string HttpPost(string url, string post, string refer = "")
        {
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "POST";
            request.UserAgent = UserAgent;
            request.Referer = refer;

            byte[] postBytes = Encoding.ASCII.GetBytes(post);
            request.ContentType = "application/x-www-form-urlencoded";
            request.ContentLength = postBytes.Length;

            Stream requestStream = request.GetRequestStream();
            requestStream.Write(postBytes, 0, postBytes.Length);
            requestStream.Close();

            HttpWebResponse response = (HttpWebResponse)request.GetResponse();
            StreamReader sr = new StreamReader(response.GetResponseStream());

            return sr.ReadToEnd();
        }

        /*End of web service*/

        public void send_http_request(string req_method, string args) {
            string data ="";
            try
            {
                if (req_method == "GET")
                    data = HttpGet(args);
                else data = HttpPost(args, "dummy", "dummy");

                JavaScriptSerializer jSerialize = new JavaScriptSerializer();
                Student view = jSerialize.Deserialize<Student>(data);
                string sb = "center = " + view.id.ToString();
                MessageBox.Show(sb);

            }

            catch {
                MessageBox.Show("There was an error fetching the data"); 
            }
        }

        private bool check_reader()
        {

        //SCardEstablishContext
            logBox1.Items.Add("Calling SCardEstablishContext...");
            retcode = ModWinsCard.SCardEstablishContext(ModWinsCard.SCARD_SCOPE_USER, 0, 0, ref hContext);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(retcode + ": "+ ModWinsCard.GetScardErrMsg(retcode));
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
            button1.Visible = true;     //Enable SCardConnect Button
            button1.Enabled = true;
            button2.Visible = false;    //Disable search reader Button 
            button2.Enabled = false;
            msgBox.Items.Insert(0, "Please place contact card of user to reader and Press OK.");
            return true;
            
        }

        private bool checkCard() {
            logBox1.Items.Add("Calling SCardConnect...");
            // Connect to the reader using hContext handle and obtain hCard handle  
            retcode = ModWinsCard.SCardConnect(hContext, comboBox1.SelectedItem.ToString(), ModWinsCard.SCARD_SHARE_EXCLUSIVE, 0 | 1, ref hCard, ref ActiveProtocol);
            if (retcode != ModWinsCard.SCARD_S_SUCCESS)
            {
                logBox1.Items.Add(retcode + ": " + ModWinsCard.GetScardErrMsg(retcode));
                msgBox.Items.Insert(0, "Please place contact card of user to reader and Press OK.");
                return false;
            }
            logBox1.Items.Add("Success!");
            return true;
        }

        private void button1_Click(object sender, EventArgs e)
        {
            if (checkCard() == true) { 
                //change form
                //groupBox1.Hide();
                bookingPanel1.Show();
            }
        }

        private void button2_Click(object sender, EventArgs e)
        {
            check_reader();
        }

        private void comboBox1_SelectedIndexChanged(object sender, EventArgs e)
        {

        }



    }

        

}


