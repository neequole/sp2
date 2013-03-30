using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace webService
{
    public class Booking
    {
        public string book_id { get; set; }
        public string title { get; set; }
        public string e_date { get; set; }
        public string e_stime { get; set; }
        public string e_etime { get; set; }
        public string venue_name { get; set; }
        public string e_tclass { get; set; }
        public string status { get; set; }
    }
}
