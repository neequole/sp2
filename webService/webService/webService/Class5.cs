using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace webService
{
    public class Event
    {
        public string id { get; set; }
        public string title { get; set; }
        public string venue_name { get; set; }
        public string e_sched_id { get; set; }
        public string e_date { get; set; }
        public string e_stime { get; set; }
        public string e_etime { get; set; }
        public string error { get; set; }
        public string error_message { get; set; }
    }
}
