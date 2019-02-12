#!/usr/local/bin/python3.5

import sys
import os
import argparse

import re
import csv

import json

parser = argparse.ArgumentParser()

infile = None
output_dir = None

parser.add_argument('-i', action='store', dest='infile',
                    help='csv file as input. (i.e. filename.csv)')
parser.add_argument('-o', action='store', dest='output_dir',
                    help='output directory as input. (i.e. $HOME)')

parser.add_argument('--version', action='version', version='%(prog)s 1.0')

results = parser.parse_args()

infile = results.infile
output_dir = results.output_dir

if(infile == None):
    print('\n')
    print('error: please use the -i option to specify the csv file as input')
    print('infile =' + ' ' + str(infile))
    print('\n')
    parser.print_help()
    sys.exit(1)
if(output_dir == None):
    print('\n')
    print('error: please use the -o option to specify the output directory as input')
    print('output_dir =' + ' ' + str(output_dir))
    print('\n')
    parser.print_help()
    sys.exit(1)


if not os.path.exists(output_dir):
    os.makedirs(output_dir)

features = []
with open(infile) as csv_file:
    csv_reader = csv.reader(csv_file, delimiter=',')
    line_count = 0
    for row in csv_reader:
        if line_count != 0:
            province = row[0]
            location = row[1]
            latlong = row[2].split(",")
            long = float(latlong[0])
            lat = float(latlong[1])
            coordinates = []
            coordinates.append(lat)
            coordinates.append(long)
            
            pathogen_common_name = row[3]
            display = row[4]
            print('province = ' + str(province))
            print('location = ' + str(location))
            print('latlong = ' + str(latlong))
            print('display = ' + str(display))
        
            coordinates_data = {}
            point_coord = {}
            properties = {}
            point_coord["type"] = "Point"
            point_coord["coordinates"] = coordinates
            
            
            coordinates_data["geometry"] = point_coord
            
        
            properties["popupContent"] = display
            
            properties["pathogenCommonName"] = pathogen_common_name
            properties["province"] = province
            properties["location"] = location
            coordinates_data["type"] = "Feature"
            coordinates_data["properties"] = properties
            coordinates_data["id"] = line_count
            features.append(coordinates_data)
        line_count += 1

geojson_data = {}
geojson_data["type"] = "FeatureCollection"
geojson_data["features"] = features
print(geojson_data)

geojson_outfile = os.path.join(output_dir, 'tracker_geojson.json')
geojson_file = open(geojson_outfile, "w+")
geojson_file.write(json.dumps(geojson_data, indent=4))
geojson_file.close()

#var bicycleRental = {
#    "type": "FeatureCollection",
#    "features": [
#                 {
#                 "geometry": {
#                 "type": "Point",
#                 "coordinates": [
#                                 -104.9998241,
#                                 39.7471494
#                                 ]
#                 },
#                 "type": "Feature",
#                 "properties": {
#                 "popupContent": "This is a B-Cycle Station. Come pick up a bike and pay by the hour. What a deal!"
#                 },
#                 "id": 51
#                 },

                 


