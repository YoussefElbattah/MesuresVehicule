from gps import *
import serial
import mysql.connector
import MySQLdb
import smbus					#import SMBus module of I2C
from time import sleep
import RPi.GPIO as GPIO
import os
import busio
import digitalio
import board
import adafruit_mcp3xxx.mcp3008 as MCP
from adafruit_mcp3xxx.analog_in import AnalogIn
from datetime import datetime
file = open("id.txt","r")
file2 = open("course.txt","r")
ide = int(file.read())
id_course = int(file2.read())
total = 0
acc_sum = 0
vitesse_sum = 0
acc_moy = 0
vitesse_moy = 0
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(14,GPIO.IN,pull_up_down = GPIO.PUD_UP)
GPIO.setup(4,GPIO.IN,pull_up_down = GPIO.PUD_UP)
#import
PWR_MGMT_1   = 0x6B
SMPLRT_DIV   = 0x19
CONFIG       = 0x1A
GYRO_CONFIG  = 0x1B
INT_ENABLE   = 0x38
ACCEL_XOUT_H = 0x3B
ACCEL_YOUT_H = 0x3D
ACCEL_ZOUT_H = 0x3F



gpsd = gps(mode=WATCH_ENABLE|WATCH_NEWSTYLE)

spi = busio.SPI(clock = board.SCK, MISO = board.MISO , MOSI = board.MOSI)

cs = digitalio.DigitalInOut(board.D22)

mcp = MCP.MCP3008(spi, cs)

chan0 = AnalogIn(mcp , MCP.P0)

def MPU_Init():
	#write to sample rate register
	bus.write_byte_data(Device_Address, SMPLRT_DIV, 7)
	
	#Write to power management register
	bus.write_byte_data(Device_Address, PWR_MGMT_1, 1)
	
	#Write to Configuration register
	bus.write_byte_data(Device_Address, CONFIG, 0)
	
	#Write to Gyro configuration register
	bus.write_byte_data(Device_Address, GYRO_CONFIG, 24)
	
	#Write to interrupt enable register
	bus.write_byte_data(Device_Address, INT_ENABLE, 1)

def read_raw_data(addr):
	#Accelero and Gyro value are 16-bit
        high = bus.read_byte_data(Device_Address, addr)
        low = bus.read_byte_data(Device_Address, addr+1)
    
        #concatenate higher and lower value
        value = ((high << 8) | low)
        
        #to get signed value from mpu6050
        if(value > 32768):
                value = value - 65536
        return value

def read_info():
    global total
    global acc_sum
    global vitesse_sum
    global ide
    report = gpsd.next() #
    if report['class'] == 'TPV' :
        acc_y = read_raw_data(ACCEL_XOUT_Y)
        Ax = round((acc_y/16384.0)*9.81,3)
        lat = getattr(report,'lat',0.0)
        lon = getattr(report,'lon',0.0)
        now = datetime.now()
        Date = now.strftime("%m/%d/%Y,%H:%M:%S")
#         date = getattr(report,'time','')
        vitesse = getattr(report,'speed','nan')
        frein = int(chan0.value*100/65000)
        total += 1
        acc_sum += Ax
        vitesse_sum += vitesse
        ide += 1
        str_ide = str(ide)
        file = open("id.txt","w")
        file.write(str_ide)
        file.seek(0)
        insert_data_accel(Ax,ide,Date,id_course)
        insert_data_vitesse(vitesse,ide,Date,id_course)
        insert_data_frein(frein,ide,Date,id_course)
        insert_data_GPS(lat,lon,Date,ide,id_course)
def insert_data_accel(accele,ide,Date,id_course):
    sqlFormula = "INSERT INTO acceleration (Accéleration,id,Date,id_course)"\
                 "VALUES (%s,%s,%s,%s)"
    args = (accele,ide,Date,id_course)
    try:
        mycursor = mydb.cursor()
        mycursor.execute(sqlFormula, args)
        mydb.commit()
        print("Accéleration inserée")
    except Exception as error:
        print(error)

def insert_data_vitesse(vitesse,ide,Date,id_course):
    sqlFormula = "INSERT INTO vitesse (Vitesse,id,Date,id_course)"\
                 "VALUES (%s,%s,%s,%s)"
    args = (vitesse,ide,Date,id_course)
    try:
        mycursor = mydb.cursor()
        mycursor.execute(sqlFormula, args)
        mydb.commit()
        print("Vitesse inserée")
    except Exception as error:
        print(error)
    
def insert_data_frein(frein,ide,Date,id_course):
    sqlFormula = "INSERT INTO frein (Frein,id,Date,id_course)"\
                 "VALUES (%s,%s,%s,%s)"
    args = (frein,ide,Date,id_course)
    try:
        mycursor = mydb.cursor()
        mycursor.execute(sqlFormula, args)
        mydb.commit()
        print("Frein inseré")
    except Exception as error:
        print(error)

def insert_data_course(v_moy,a_moy,Date_fin,id_course):
    sqlFormula = "INSERT INTO course (Vitesse_moyenne,Accéleration_moyenne,Date_fin,id_course)"\
                 "VALUES (%s,%s,%s,%s)"
    args = (v_moy,a_moy,Date_fin,id_course)
    try:
        mycursor = mydb.cursor()
        mycursor.execute(sqlFormula, args)
        mydb.commit()
        print("Data de course inserée")
    except Exception as error:
        print(error)
    

def insert_data_GPS(Lat,Lon,Date,id_GPS,id_course):
    sqlFormula = "INSERT INTO point_gps (Longitude,Latitude,Date,id_Point_GPS,id_course)"\
                 "VALUES (%s,%s,%s,%s,%s)"
    args = (Lat,Lon,Date,id_GPS,id_course)
    try:
        mycursor = mydb.cursor()
        mycursor.execute(sqlFormula, args)
        mydb.commit()
        print("Data de GPS inserée")
    except Exception as error:
        print(error)
        
bus = smbus.SMBus(1) 	# or bus = smbus.SMBus(0) for older version boards
Device_Address = 0x68   # MPU6050 device address

MPU_Init()
try:
        mydb = mysql.connector.connect(
        host="localhost",
        user="root",
        passwd="youssef",
        database="projet"
        )
        print(mydb)
except Exception as error:
        print(error)
while True:
    if GPIO.input(14) == False :
        while 1 :
            read_info()
            if GPIO.input(4) == False :
                now = datetime.now()
                Date_fin = now.strftime("%m/%d/%Y %H:%M:%S")
                acc_moy = acc_sum/total
                vitesse_moy = vitesse_sum/total
                insert_data_course(vitesse_moy,acc_moy,Date_fin,id_course)
                file2.seek(0)
                id_course += 1
                str_id_c = str(id_course)
                file2 = open("course.txt","w")
                file2.write(str_id_c)
                file2.seek(0)
                break
            
        




