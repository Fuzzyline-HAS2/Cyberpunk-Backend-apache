<?php
    $DBhost = "localhost";
    $DBuser = "root";
    $DBpasswd = "Code3824@";
    $DBname = "has2";

    $DB_request = $_GET["request"];
    $DB_table = $_GET["table"];
    $DB_key = $_GET["key"];
    $DB_mac = $_GET["mac"];
    $DB_column = $_GET["column"];
    $DB_value = $_GET["value"];

    $conn = mysqli_connect($DBhost,$DBuser,$DBpasswd,$DBname);

    // if($DB_key[0] == 'G'){
    //     $DB_table = "iotglove";
    // }
    // else if($DB_key[1] == 'D'){
    //     $DB_table = "tagmachine";
    // }
    // else if($DB_key[1] == 'L'){
    //     $DB_table = "lifemachine";
    // }
    // else if($DB_key[1] == 'I'){
    //     $DB_table = "itembox";
    // }
    // else if($DB_key[1] == 'G'){
    //     $DB_table = "generator";
    // }
    // else if($DB_key[1] == 'E'){
    //     $DB_table = "escapemachine";
    // }
    // else if($DB_key[1] == 'T'){
    //     $DB_table = "temple";
    // }

    switch($DB_request){
        case "ReceiveMine" :
            //[table]device : shift_machine = 0으로 만들어 주는 쿼리 
            //[table]device : mac으로 name,type,theme 찾는 쿼리
            $select = "SELECT device_name,device_type,theme FROM $DB_table WHERE mac = \"$DB_mac\"";
            $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
            //자기 장치 데이터 수신 쿼리
            if($device_array['device_type'] !== 'mp3'){
                $select = "UPDATE $DB_table SET shift_machine = 0 WHERE mac = \"$DB_mac\"";
                $result = mysqli_query($conn, $select);
                if($device_array['theme'] == 'waiting'){
                    $select = "SELECT * FROM $DB_table WHERE device_name = '{$device_array['device_name']}'";
                }
                else{
                    $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = '{$device_array['device_name']}'";
                }
            }
            break;
        case "Receive" :
            //[table]device : 장치명으로 name,type,theme 찾는 쿼리
            $select = "SELECT device_name,device_type,theme FROM $DB_table WHERE device_name = \"$DB_key\"";
            $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
            //원하는 장치 데이터 수신 쿼리
            $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = '{$device_array['device_name']}'";
            break;
        case "ReceiveMP3" :
            //[table]device : 장치명으로 name,type,theme 찾는 쿼리
            $select = "UPDATE $DB_table SET shift_machine = shift_machine-1 WHERE device_name = \"$DB_key\"";
            $result = mysqli_query($conn, $select);
            $select = "SELECT device_name,device_type,theme FROM $DB_table WHERE device_name = \"$DB_key\"";
            $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
            //원하는 장치 데이터 수신 쿼리
            $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = $DB_value";
            break;
        case "Loop" :
            $select = "SELECT shift_machine,watchdog FROM $DB_table WHERE mac = \"$DB_mac\"" ;
            break;
        default :
            break;
    }   

    if($DB_request == "Send"){
        //[table]device : 장치명으로 name,type,theme,shift_machine 찾는 쿼리
        $select = "SELECT device_name,device_type,theme,shift_machine FROM $DB_table WHERE device_name = \"$DB_key\"";
        $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
        //원하는 장치 데이터 수신 쿼리
        $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = \"$DB_key\"";
        $theme_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
        //send 데이터 쿼리 만들어 전송하는 작업 
        switch($DB_column){
            case "restart":
                $select = "UPDATE $DB_table SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "life_chip":
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = life_chip + $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "taken_chip":
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = taken_chip + $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "battery_pack":
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = battery_pack + $DB_value WHERE device_name = '{$device_array['device_name']}'";
                echo($select);
                mysqli_query($conn, $select);
                break;
            case "role" :
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = \"$DB_value\" WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "exp": 
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = exp + $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                $now_exp = $theme_array['exp'] + $DB_value;
                if($now_exp >= $theme_array['max_exp']){
                    $select =  "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET exp = exp - max_exp, max_exp = max_exp + 10, lv = lv + 1 WHERE device_name = '{$device_array['device_name']}'";
                    mysqli_query($conn, $select);
                    $select =  "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET skill_point = skill_point + 1";
                    mysqli_query($conn, $select);
                }
                break;
            case "folder_num": 
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "file_num": 
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            default :
                break;
        }
        //shift_machine +1 해주는 부분. send를 하고난 뒤 항상 더해주기 
        if($device_array['device_type'] !== mp3){
            $select = "UPDATE $DB_table SET shift_machine = {$device_array['shift_machine']} + 1 WHERE device_name = '{$device_array['device_name']}'";
        }
        //send 요청시 node.js로 새로고침 요청
        if($device_array['device_type'] === 'iotglove'){
            file_get_contents('http://localhost:5000/api/iotglove_php_request');
        }
        else {
            if($device_array['theme'] === 'cyberpunk'){
                file_get_contents('http://localhost:5000/api/cyberpunk_php_request');
            }
            else if($device_array['theme'] === 'deadland'){
                file_get_contents('http://localhost:5000/api/deadland_php_request');
            }
        }
    }

    $result = mysqli_query($conn, $select);

    if($DB_request != "Send"){ 
        while($row = mysqli_fetch_assoc($result)){
            $json_DB = json_encode($row);
            echo $json_DB;
        }
    }


    mysqli_close($conn);
?>