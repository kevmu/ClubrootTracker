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
            lat = float(row[2])
            long = float(row[3])
			
            coordinates = []
            coordinates.append(lat)
            coordinates.append(long)
            
            pathogen_common_name = row[4]
            display = row[5]
            print('province = ' + str(province))
            print('location = ' + str(location))
            print('lat = ' + str(lat))
            print('long = ' + str(long))
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

geojson_outfile = os.path.join(output_dir, 'tracker_geojson.js')
geojson_file = open(geojson_outfile, "w+")
geojson_file.write("var clubroot_tracker_coords" + " " + "=" + " " + json.dumps(geojson_data, indent=4))
geojson_file.close()



