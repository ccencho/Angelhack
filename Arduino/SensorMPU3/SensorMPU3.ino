
// www.arduinesp.com
//
// Plot DTH11 data on thingspeak.com using an ESP8266
// April 11 2015
// Author: Jeroen Beemster
// Website: www.arduinesp.com
#include "I2Cdev.h"
#include "MPU6050.h"

// Arduino Wire library is required if I2Cdev I2CDEV_ARDUINO_WIRE implementation
// is used in I2Cdev.h
#if I2CDEV_IMPLEMENTATION == I2CDEV_ARDUINO_WIRE
    #include "Wire.h"
#endif

// class default I2C address is 0x68
// specific I2C addresses may be passed as a parameter here
// AD0 low = 0x68 (default for InvenSense evaluation board)
// AD0 high = 0x69
MPU6050 accelgyro;
//MPU6050 accelgyro(0x69); // <-- use for AD0 high

int16_t ax, ay, az;
int16_t gx, gy, gz;

#define OUTPUT_READABLE_ACCELGYRO

#include <ESP8266WiFi.h>
#include <Wire.h>
// replace with your channel’s thingspeak API key,
const char* ssid = "redpucp";
const char* password = "C9AA28BA93";

const char* server = "demo-alonso1978.rhcloud.com";
int pitch;
int roll;
WiFiClient client;



void setup() {
    #if I2CDEV_IMPLEMENTATION == I2CDEV_ARDUINO_WIRE
        Wire.begin();
    #elif I2CDEV_IMPLEMENTATION == I2CDEV_BUILTIN_FASTWIRE
        Fastwire::setup(400, true);
    #endif
    Serial.println("Initializing I2C devices...");
    accelgyro.initialize();

    // verify connection
    Serial.println("Testing device connections...");
    Serial.println(accelgyro.testConnection() ? "MPU6050 connection successful" : "MPU6050 connection failed");
    
    
    Serial.begin(115200);
    delay(10);
    
    WiFi.begin(ssid, password);
    
    Serial.println();
    Serial.println();
    Serial.print("Connecting to ");
    Serial.println(ssid);
    
    WiFi.begin(ssid, password);
    
    while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    }
    Serial.println("");
    Serial.println("WiFi connected"); 

}

void loop() {

    int pitch2=pitch;
    int roll2=roll;
    String Data;
    accelgyro.getMotion6(&ax, &ay, &az, &gx, &gy, &gz);

    // these methods (and a few others) are also available
    //accelgyro.getAcceleration(&ax, &ay, &az);
    //accelgyro.getRotation(&gx, &gy, &gz);

    #ifdef OUTPUT_READABLE_ACCELGYRO
        // display tab-separated accel/gyro x/y/z values
        Serial.print("a/g:\t");
    #endif
    float roll1 = atan2(ay, az);
    float pitch1 = atan2(-ax, sqrt(ay * ay + az * az));
  
  pitch1 *= 180.0 / PI;
  roll1  *= 180.0 / PI;
  pitch =pitch1;
  roll=roll1;
  
  Serial.print("Pitch, Roll: ");
  Serial.print(pitch);
  Serial.print(", ");
  Serial.println(roll);
  Serial.println(Data);

    String pitchsend= String(pitch);
    String rollsend= String(roll);

    int sensor1=0;    // sensor IMU: envia estado 0,1,2,

    if(((pitch<15)&&(pitch>-15))|| ((roll<15)&&(roll>-15)))
    {
      Data="0";
    }
    if (((pitch>-30)&&(pitch<-15))||((pitch<30)&&(pitch>15))||((roll>-30)&&(roll<-15))||((roll<30)&&(roll>15)))
    {
      Data="1";
    }
    if((pitch<-30)||(pitch>30)||(roll<-30)||(roll>30))
    {
      Data="2";
    }

    if((pitch!=pitch2)||(roll!=roll2))
   {    
    String PostData = "Colegio=1&ID=1";
    PostData +="&sensor1=";
    PostData += String(pitchsend);
    PostData +=",";
    PostData += String(rollsend);
     PostData +=";";
    PostData += Data;
    
    if (client.connect(server,80)) { 
    
    
    client.println("POST /sensores/nuevo/ HTTP/1.1");
    client.println("Host: demo-alonso1978.rhcloud.com");
    client.println("Cache-Control: no-cache");
    client.println("Content-Type: application/x-www-form-urlencoded");
    client.print("Content-Length: ");
    client.println(PostData.length());
    client.println();
    client.println(PostData);
    Serial.print("ul1: \n");
    Serial.println("% send to server");
    }
    client.stop();
    
    Serial.println("Waiting…");
    delay(4000);
    PostData = "";
   }
}

