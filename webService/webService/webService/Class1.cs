using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
//for HTTP REQUEST
using System.Net;
using System.IO;
//for JSON
using System.Web.Script.Serialization;



namespace webService
{
    public class WebService
    {
        /*for web client*/
        public string UserAgent = @"Mozilla/5.0 (Windows; Windows NT 6.1) AppleWebKit/534.23 (KHTML, like Gecko) Chrome/11.0.686.3 Safari/534.23";
        /*end of elements for web client*/

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

    }
}
