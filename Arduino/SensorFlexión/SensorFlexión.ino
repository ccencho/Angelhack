// **************** FLEX *******************************
// www.arduinesp.com
//
// Plot DTH11 data on thingspeak.com using an ESP8266
// April 11 2015
// Author: Jeroen Beemster
// Website: www.arduinesp.com

 
#include <ESP8266WiFi.h>
 
// replace with your channel’s thingspeak API key,
const char* ssid = "redpucp";
const char* password = "C9AA28BA93";
const char* server = "demo-alonso1978.rhcloud.com";
int sensor_flex = A0 ;    // declara pin3 como led
int datoFlex;
int estadoFlex;
String PostData;
const int led_verde=16;
const int led_amar=5;
const int led_rojo=4;
WiFiClient client;
 
void setup() {

pinMode(led_verde,OUTPUT);
pinMode(led_amar,OUTPUT);
pinMode(led_rojo,OUTPUT);
  
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
 
//int sensor1=0;    // sensor IMU: envia estado 0,1,2,
//int sensor2=1;    // sensor flex:  0,1,2
//int sensor3=0;   // Nivel de agu: 0,1
//float sensor4=0;   // Nivel voltaje: float

 int datoFlex=analogRead(sensor_flex);
 //Serial.print("valor: ");
 //Serial.print(datoFlex);
 //Serial.println();
 
if (datoFlex>220 && datoFlex<260) 
{ digitalWrite(led_verde,HIGH);
  digitalWrite(led_amar,LOW); 
  digitalWrite(led_rojo,LOW); 
  estadoFlex=0;}

if (datoFlex>140 && datoFlex<220) 
{ digitalWrite(led_verde,LOW);
  digitalWrite(led_amar,HIGH); 
  digitalWrite(led_rojo,LOW); 
  estadoFlex=1;}

if (datoFlex>109 && datoFlex<140) 
{ digitalWrite(led_verde,LOW);
  digitalWrite(led_amar,LOW); 
  digitalWrite(led_rojo,HIGH); 
  estadoFlex=2;} 
 
if (client.connect(server,80)) { 
//******************** Sensor Flex ***************

//************************************************

PostData = "Colegio=2&ID=2";
//PostData +="&sensor1=";
//PostData += String(sensor1);
//PostData +=",";
//PostData += String(altura);
PostData +="&sensor2=";        // Flex
PostData += String(datoFlex);
PostData += ";";
PostData += String(estadoFlex);
//PostData +="&sensor3=";
//PostData += String(sensor3);
//PostData +="&sensor4=";
//PostData += String(sensor4);


client.println("POST /sensores/nuevo/ HTTP/1.1");
client.println("Host: demo-alonso1978.rhcloud.com");
client.println("Cache-Control: no-cache");
client.println("Content-Type: application/x-www-form-urlencoded");
client.print("Content-Length: ");
client.println(PostData.length());
client.println();
client.println(PostData);
char c = +client.read();
Serial.print("ul1: \n");
Serial.print(c);
Serial.println("% send to server");
 }
 
client.stop();
Serial.println("Waiting…");
delay(3000);
PostData = "";
}

