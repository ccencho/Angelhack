
// www.arduinesp.com
//
// Plot DTH11 data on thingspeak.com using an ESP8266
// April 11 2015
// Author: Jeroen Beemster
// Website: www.arduinesp.com
#include <Wire.h>
#include <ESP8266WiFi.h>



const char* ssid = "redpucp";
const char* password = "C9AA28BA93";

const char* server = "demo-alonso1978.rhcloud.com";
const int PinTrig = D3;
const int PinEcho = D4;
const float VelocSonido = 34000.0;
float distancia;

WiFiClient client;

void setup() {
  Serial.begin(115200);
  delay(10);

   pinMode(PinTrig, OUTPUT);
  pinMode(PinEcho, INPUT);
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
  int sensor3;
  int Ultrason=sensor3;
  String Data;
  digitalWrite(PinTrig, LOW);
  delayMicroseconds(2);

  digitalWrite(PinTrig, HIGH);
  delayMicroseconds(10);
  digitalWrite(PinTrig, LOW);

  unsigned long tiempo = pulseIn(PinEcho, HIGH);
  
  // Obtenemos la distancia en cm, hay que convertir el tiempo en segudos ya que está en microsegundos
  // por eso se multiplica por 0.000001
  distancia = tiempo * 0.000001 * VelocSonido / 2.0;
  Serial.print(distancia);
  Serial.print("cm");
  Serial.println();


  sensor3=distancia;  
  

  if(distancia>15)
  {
    Data="0";
  }else if((distancia<15)&&(distancia>9))
  {
    Data="1";
  }else if (distancia <9)
  {
    Data="2";
  }


  if (sensor3!= Ultrason)
  {
  String PostData = "Colegio=3&ID=3";
  PostData +="&sensor3=";
  PostData += String(sensor3);
  PostData +=";";
  PostData += Data;
  
  
  if (client.connect(server,80)) { 
  client.println("POST /sensores/nuevo HTTP/1.1");
  client.println("Host: demo-alonso1978.rhcloud.com");
  client.println("Cache-Control: no-cache");
  client.println("Content-Type: application/x-www-form-urlencoded");
  client.print("Content-Length: ");
  client.println(PostData.length());
  client.println();
  client.println(PostData);
  
  
  Serial.print("ul1: ");

  Serial.println("% send to server");
  }
  client.stop();
  
  Serial.println("Waiting…");
  delay(4000);
  PostData="";
  }
}

