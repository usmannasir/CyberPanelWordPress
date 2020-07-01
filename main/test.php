<?php

$test = '{
  "server": {
    "id": 6509900,
    "name": "hello",
    "status": "initializing",
    "created": "2020-07-01T09:24:39+00:00",
    "public_net": {
      "ipv4": {
        "ip": "78.46.184.35",
        "blocked": false,
        "dns_ptr": "static.35.184.46.78.clients.your-server.de"
      },
      "ipv6": {
        "ip": "2a01:4f8:c0c:f0d3::/64",
        "blocked": false,
        "dns_ptr": []
      },
      "floating_ips": []
    },
    "private_net": [],
    "server_type": {
      "id": 1,
      "name": "cx11",
      "description": "CX11",
      "cores": 1,
      "memory": 2.0,
      "disk": 20,
      "deprecated": null,
      "prices": [
        {
          "location": "fsn1",
          "price_hourly": {
            "net": "0.0040000000",
            "gross": "0.0040000000000000"
          },
          "price_monthly": {
            "net": "2.4900000000",
            "gross": "2.4900000000000000"
          }
        },
        {
          "location": "hel1",
          "price_hourly": {
            "net": "0.0040000000",
            "gross": "0.0040000000000000"
          },
          "price_monthly": {
            "net": "2.4900000000",
            "gross": "2.4900000000000000"
          }
        },
        {
          "location": "nbg1",
          "price_hourly": {
            "net": "0.0040000000",
            "gross": "0.0040000000000000"
          },
          "price_monthly": {
            "net": "2.4900000000",
            "gross": "2.4900000000000000"
          }
        }
      ],
      "storage_type": "local",
      "cpu_type": "shared"
    },
    "datacenter": {
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
    "image": {
      "id": 17355329,
      "type": "snapshot",
      "status": "available",
      "name": null,
      "description": "v2.0.1-new",
      "image_size": 2.27163407714844,
      "disk_size": 20,
      "created": "2020-06-03T19:57:55+00:00",
      "created_from": {
        "id": 6109738,
        "name": "v2.0.1"
      },
      "bound_to": null,
      "os_flavor": "centos",
      "os_version": null,
      "rapid_deploy": false,
      "protection": {
        "delete": false
      },
      "deprecated": null,
      "labels": {}
    },
    "iso": null,
    "rescue_enabled": false,
    "locked": false,
    "backup_window": null,
    "outgoing_traffic": 0,
    "ingoing_traffic": 0,
    "included_traffic": 21990232555520,
    "protection": {
      "delete": false,
      "rebuild": false
    },
    "labels": {},
    "volumes": []
  },
  "action": {
    "id": 74738062,
    "command": "create_server",
    "status": "running",
    "progress": 0,
    "started": "2020-07-01T09:24:39+00:00",
    "finished": null,
    "resources": [
      {
        "id": 6509900,
        "type": "server"
      }
    ],
    "error": null
  },
  "next_actions": [
    {
      "id": 74738060,
      "command": "start_server",
      "status": "running",
      "progress": 0,
      "started": "2020-07-01T09:24:39+00:00",
      "finished": null,
      "resources": [
        {
          "id": 6509900,
          "type": "server"
        }
      ],
      "error": null
    }
  ],
  "root_password": "Vsdjp94HduXhkkugTXtC"
}
';

$data = json_decode($test);

echo $data->server->id;