using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace webService
{
    public class BookingClass
    {
        public string id { get; set; }
	    public string booking_id { get; set; }
	    public string class_id { get; set; }
	    public string timein { get; set; }
	    public string timeout { get; set; }
        public string error { get; set; }
        public string error_message { get; set; }
    }
}
