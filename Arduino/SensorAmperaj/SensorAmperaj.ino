
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
float Sensibilidad=0.185; //Sensibilidad

WiFiClient client;
float get_corriente(int n_muestras)
{
  float voltajeSensor;
  float corriente=0;
  
  for(int i=0;i<n_muestras;i++)
  {
     voltajeSensor=5.00/1023*analogRead(A0);////lectura del sensor
    corriente=corriente+(voltajeSensor-2.5)/Sensibilidad; //Ecuación  para obtener la corriente
  }
  corriente=corriente/n_muestras;
  return(corriente);
}


void setup() {
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
    float sensor4;
    int sensor44=sensor4;
    String PostData = "Colegio=4&ID=4";
    String data;
    float corriente=get_corriente(100);//obtenemos la corriente promedio de 500 muestras 
    sensor4=7-corriente;   // Nivel voltaje: float
    Serial.print("Corriente: ");
    Serial.println(corriente,3);
    Serial.print("Corriente: ");
    Serial.println(sensor4,3); 
    
    
    if(sensor4 < 0.5)
    {
      String data= "2";  
    }else
    {
      data="0";
    }
   
    PostData +="&sensor4=";
    PostData += String(sensor4);
    PostData +=";";
    PostData+= data;
    if(sensor44!=sensor4)
    {
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


