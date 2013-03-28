using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace webService
{
        public class AdminUser
        {
            public string usrname { get; set; }
            public string pwd { get; set; }
            public string fname { get; set; }
            public string mname { get; set; }
            public string lname { get; set; }
            public string suffix { get; set; }
            public string sex { get; set; }
            public string cnum { get; set; }
            public string email { get; set; }
            public int id { get; set; }
            public string error { get; set; }
            public string error_message { get; set; }
        }
}
