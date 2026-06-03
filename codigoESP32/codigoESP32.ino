#include <WiFi.h>
#include <HTTPClient.h>
#include "DHT.h"

#define DHTPIN 22
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

const char* ssid      = "UADEO-ESP32";
const char* password  = "isof2026";
// ✅ http:// añadido
const char* serverUrl = "http://192.168.1.207/user-23060120/Dashboard-muestra-de-temperatura-de-lugares-mediante-una-grafica/recibe.php";
const char* email     = "230a6q01ss230@uadeo.mx";
const char* clave     = "58de3cd3c3ab4aaedf8dbceffce0d9d9";
int lugar_id = 16;   // ✅ backtick eliminado

void setup() {
  Serial.begin(115200);
  dht.begin();
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConectado a WiFi");
}

void loop() {
  float temperatura = dht.readTemperature();
  if (!isnan(temperatura)) {
    enviarDatos(temperatura);
  } else {
    Serial.println("Error leyendo temperatura");
  }
  delay(10000);
}

void enviarDatos(float temp) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String body =
      "temperatura=" + String(temp, 2) +
      "&lugar_id="   + String(lugar_id) +
      "&email="      + String(email) +
      "&password="   + String(clave);

    int code = http.POST(body);
    Serial.print("HTTP Code: ");
    Serial.println(code);         // muestra el código de respuesta

    if (code > 0) {
      Serial.println(http.getString());
    } else {
      Serial.print("Error POST: ");
      Serial.println(http.errorToString(code)); // ✅ mensaje de error detallado
    }
    http.end();
  } else {
    Serial.println("WiFi desconectado");
  }
}