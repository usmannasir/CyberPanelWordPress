<?php


$date = new DateTime();

//Create a new DateInterval object using P30D.
$interval = new DateInterval('P31D');

//Add the DateInterval object to our DateTime object.
$date->add($interval);

//Print out the result.
//echo $date->format("Y-m-d");

$now = new DateTime();

$interval = $date->getTimestamp() - $now->getTimestamp();
echo $interval;

$data = '{
  "datacenters": [
    {
      "id": 2,
      "name": "nbg1-dc3",
      "description": "Nuremberg 1 DC 3",
      "location": {
        "id": 2,
        "name": "nbg1",
        "description": "Nuremberg DC Park 1",
        "country": "DE",
        "city": "Nuremberg",
        "latitude": 49.452102,
        "longitude": 11.076665,
        "network_zone": "eu-central"
      },
      "server_types": {
        "supported": [
          2,
          4,
          6,
          8,
          10,
          9,
          7,
          5,
          3,
          1,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ],
        "available": [
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          10,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25
        ],
        "available_for_migration": [
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          10,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ]
      }
    },
    {
      "id": 3,
      "name": "hel1-dc2",
      "description": "Helsinki 1 DC 2",
      "location": {
        "id": 3,
        "name": "hel1",
        "description": "Helsinki DC Park 1",
        "country": "FI",
        "city": "Helsinki",
        "latitude": 60.169855,
        "longitude": 24.938379,
        "network_zone": "eu-central"
      },
      "server_types": {
        "supported": [
          2,
          4,
          6,
          8,
          10,
          9,
          7,
          5,
          3,
          1,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ],
        "available": [
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          10,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ],
        "available_for_migration": [
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          10,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ]
      }
    },
    {
      "id": 4,
      "name": "fsn1-dc14",
      "description": "Falkenstein 1 DC14",
      "location": {
        "id": 1,
        "name": "fsn1",
        "description": "Falkenstein DC Park 1",
        "country": "DE",
        "city": "Falkenstein",
        "latitude": 50.47612,
        "longitude": 12.370071,
        "network_zone": "eu-central"
      },
      "server_types": {
        "supported": [
          2,
          4,
          6,
          8,
          10,
          9,
          7,
          5,
          3,
          1,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ],
        "available": [
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          10,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ],
        "available_for_migration": [
          1,
          2,
          3,
          4,
          5,
          6,
          7,
          8,
          9,
          10,
          11,
          12,
          13,
          14,
          15,
          22,
          23,
          24,
          25,
          26
        ]
      }
    }
  ],
  "recommendation": 3,
  "meta": {
    "pagination": {
      "page": 1,
      "per_page": 25,
      "previous_page": null,
      "next_page": null,
      "last_page": 1,
      "total_entries": 3
    }
  }
}';

$json = json_decode($data);

foreach ($json->datacenters as $datacenter){
    echo $datacenter->location->name;
    echo $datacenter->location->city;
}



