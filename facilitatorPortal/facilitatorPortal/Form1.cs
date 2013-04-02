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
        List<Event> allEvent = new List<Event>();
        List<Booking2> allBooking = new List<Booking2>();
        string event_sched_id;

        public Form1()
        {
            InitializeComponent();
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
                while (button3.Enabled == true) {
                    logBox1.Items.Add("Checking for cards.....");
                }
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
            button3.Enabled = false;
            button1.Enabled = true;
        }
    }
}
