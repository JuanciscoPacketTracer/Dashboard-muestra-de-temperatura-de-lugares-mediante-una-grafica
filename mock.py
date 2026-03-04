import mysql.connector
import random
import time
import math
from datetime import datetime
conexion=mysql.connector.connect(host="127.0.0.1",user="root",password="rootroot",database="roomtemperaturedb")
cursor=conexion.cursor()
config_lugares = {
    4: {'base': 23, 'amplitud': 4},
    13: {'base': 18, 'amplitud': 2},
    14: {'base': 25, 'amplitud': 5}
}
intervalo_segundos = 10
while True:
    ahora=datetime.now()
    segundos_del_dia = ahora.hour*3600 + ahora.minute*60 + ahora.second
    for lugar, config in config_lugares.items():
        base = config['base']
        amplitud = config['amplitud']
        ruido=random.uniform(-0.15, 0.15)
        temperatura=base+amplitud*math.sin(2*math.pi*segundos_del_dia/86400)+ruido
        temperatura=round(temperatura,2)
        
        cursor.execute(
            "INSERT INTO Temperaturas(FechaTemperatura,Lugares_IdLugar,ValorTemperatura) VALUES(%s,%s,%s)",
            (ahora,lugar,temperatura)
        )
        print("Insertado:",ahora,"Lugar:",lugar,"Temp:",temperatura)
    conexion.commit()
    time.sleep(intervalo_segundos)