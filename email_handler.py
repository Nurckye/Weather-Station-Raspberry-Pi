# -*- coding: utf-8 -*-
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.base import MIMEBase
from email import encoders

import sys
from datetime import date
import time
import sqlite3
from sqlite3 import Error
import csv

system_time = time.localtime(time.time())
month_dict = {
    1: "January",
    2: "February",
    3: "March",
    4: "April",
    5: "May",
    6: "June",
    7: "July",
    8: "August",
    9: "September",
    10: "October",
    11: "November",
    12: "December"
}

def make_csv(month, day):
    database = "weather_database.db"
    try:
        first = True;
        res = None;
        conn = sqlite3.connect(database)
        cur = conn.cursor()
        cur.execute("SELECT * FROM weather_data WHERE month_day = ? AND month = ? ORDER BY seconds DESC",
                    (day, month))
        rows = cur.fetchall()
        with open('weather_file.csv', mode='w') as weather_file:
            weather_report = csv.writer(weather_file, delimiter=',', quotechar='"',
                                        quoting=csv.QUOTE_MINIMAL)
            weather_report.writerow(['LIGHT', 'PRESSURE', 'TEMPERATURE', 'HUMIDITY', 'DAY TYPE',
                                    'RAINY', 'MONTH', 'MONTH_DAY', 'WEEK_DAY', 'HOUR', 'SECONDS'])
            for row in rows:
                if first:
                    res = (row[3], row[4]) #temperature, humidity
                weather_report.writerow(row[1:])
            if res is None:
                print "whoops"
                exit(1)
            return res
    except Error as e:
        print(e)

firstname = sys.argv[1]
lastname = sys.argv[2]
gender = 'mr.' if sys.argv[3] == 'male' else 'mrs.'

month = month_dict[system_time.tm_mon]
day = system_time.tm_mday

(temperature, humidity) = make_csv(month, day)

fromaddr = "radu.nitescu35@gmail.com"
toaddr = sys.argv[4]
msg = MIMEMultipart()

msg['From'] = fromaddr
email_password = "soarecele"
msg['To'] = toaddr
msg['Subject'] = 'Weather today - %s' % date.today().strftime("%B %d, %Y")

body = """
Hello %s %s %s,

Right now there are %s‎°C with a humidity of %s%%.
A full report of the weather for today is attached in this email.

---
Best regards,
John Smith @ Weather Station
""" % (gender, firstname, lastname, temperature, humidity)


msg.attach(MIMEText(body, 'plain'))

filename = "weather_file.csv"
attachment = open("weather_file.csv", "rb")

p = MIMEBase('application', 'octet-stream')

p.set_payload((attachment).read())

encoders.encode_base64(p)

p.add_header('Content-Disposition', "attachment; filename= %s" % filename)
print "FFF"
msg.attach(p)
s = smtplib.SMTP('smtp.gmail.com', 587)
s.starttls()
s.login(fromaddr, email_password)
text = msg.as_string()
s.sendmail(fromaddr, toaddr, text)
s.quit()
