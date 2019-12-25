import sqlite3
import time
import datetime
import socket

def get_time():
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
    week_dict = {
        0: "Monday",
        1: "Tuesday",
        2: "Wednesday",
        3: "Thursday",
        4: "Friday",
        5: "Sunday",
        6: "Sunday"
    }

    if system_time.tm_hour < 10 :
        hour = '0'+ str(system_time.tm_hour)
    else:
        hour = str(system_time.tm_hour)
    if system_time.tm_min < 10 :
        min = '0'+ str(system_time.tm_min)
    else:
        min = str(system_time.tm_min)
    time_clock = hour + ':' + min

    now = datetime.datetime.now()
    midnight = now.replace(hour=0, minute=0, second=0, microsecond=0)
    seconds_since_midnight = (now - midnight).seconds

    return (month_dict[system_time.tm_mon], system_time.tm_mday,
            week_dict[system_time.tm_wday], time_clock, seconds_since_midnight)

def check_data(light_value, pressure, temperature, humidity):
    if float(temperature) < -10 or float(temperature) > 40:
        return False
    if float(humidity) > 100:
        return False
    return True
   

def compute_data(light_value, pressure, temperature, humidity):
    day_type = "Cloudy" if light_value == 0 else "Clear" #PLACEHOLDER TO DO
    rainy = "True" if float(humidity) > 80 else "False"
    return (light_value, pressure, temperature, humidity, day_type, rainy) + get_time()

def create_connection(db_file):
    try:
        conn = sqlite3.connect(db_file)
        return conn
    except Error as e:
        print(e)
    return None

def create_table(conn, create_table_sql):
    try:
        c = conn.cursor()
        c.execute(create_table_sql)
    except Error as e:
        print(e)

def insert_values(conn, parameters):
    cur = conn.cursor()
    sql = """ INSERT INTO weather_data(light_value, pressure, temperature, humidity, day_type, rainy, month, month_day, week_day, hour, seconds)
              VALUES(?,?,?,?,?,?,?,?,?,?,?) """
    cur.execute(sql, parameters)
    conn.commit()

s = socket.socket()
port = 12345
s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

s.bind(('0.0.0.0', port))
s.listen(5)
def main():
    database = "weather_database.db"

    weather_information_table = """ CREATE TABLE IF NOT EXISTS weather_data (
                                        id integer PRIMARY KEY AUTOINCREMENT,
                                        light_value FLOAT NOT NULL,
                                        pressure FLOAT NOT NULL,
                                        temperature FLOAT NOT NULL,
                                        humidity FLOAT NOT NULL,
                                        day_type TEXT NOT NULL,
                                        rainy TEXT NOT NULL,
                                        month TEXT NOT NULL,
                                        month_day TEXT NOT NULL,
                                        week_day TEXT NOT NULL,
                                        hour TEXT NOT NULL,
                                        seconds INTEGER NOT NULL
                                    ); """

    conn = create_connection(database)
    create_table(conn, weather_information_table)
    if conn is not None:
        while True:
            print("conexx")
            c, addr = s.accept()
            while True:
                try:
                    msj=c.recv(1024)
                    print msj
                    cur = conn.cursor()
                    if msj == '':
                        c.close()
                        break
                    raw_data = tuple(msj.split('_'))
                    if raw_data[0] != 'header':
                        c.close()
                        break
                    data = compute_data(raw_data[1], raw_data[2], raw_data[3], raw_data[4])
                    print(data)
                    if check_data(raw_data[1], raw_data[2], raw_data[3], raw_data[4]):
                        print("Valid Data - inserting . . .")
                        insert_values(conn, data)
                    else:
                        print("Invalid set of values!")
                    time.sleep(1)
                except socket.error:
                    break
    else:
        print("Error! Database connection can not be created!")


if __name__ == '__main__':
    main()
