<?php
return [
    "connect"=>[
        "server"=>[
            "ip"=>"127.0.0.1",
            "port"=>2206,
            "proc_count"=>1
        ],
        "client"=>[
            "port"=>8082,
            "ip"=>"0.0.0.0",
            "proc_count"=>4,
            "ssl"=>false,
            "content"=>[
                "ssl"=>[
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ]
            ]
        ]
    ],
    "container"=>[
        "send"=>["forward","debug"],
        "listen"=>["bind","debug"],
        "query"=>["debug"],
        "close"=>["debug"],
    ],
    "event"=>[
        "onBegin"=>[
            "forward"=>"Bboyyue\Channel\worker\method\forward",
            "bind"=>"Bboyyue\Channel\worker\method\bind",
        ],
        "onEnd"=>[
            "debug"=>"Bboyyue\Channel\worker\method\debug"
        ],
        "forTapType"=>[]
    ]
];