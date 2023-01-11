<?php
    //mysql과 연결하기 위한 정보를 저장하는 변수
    $DBhost = "localhost";
    $DBuser = "root";
    $DBpasswd = "Code3824@";
    $DBname = "has2";

    //ESP에서 전송하는 정보를 저장하는 변수
    $DB_request = $_GET["request"]; //어떤 요청을 하는가? ReceiveMine,Receive,ReceiveMP3,Loop,Send
    $DB_table = $_GET["table"]; //어떤 테이블을 사용하는가? - 기본적으로 device 테이블을 사용
    $DB_key = $_GET["key"]; //PK로 설정된 column. 기본적으로 device_name을 가리킴
    $DB_mac = $_GET["mac"]; //device MAC 주소. 요청 보낸 장치의 MAC
    //Send에서만 사용하는 변수
    $DB_column = $_GET["column"]; //변경할 column
    $DB_value = $_GET["value"]; //column 값

    //mysql server에 연결하는 함수 
    $conn = mysqli_connect($DBhost,$DBuser,$DBpasswd,$DBname);

    switch($DB_request){
        case "ReceiveMine" : //요청한 장치의 MAC을 이용해 자기 자신의 데이터를 수신
            //device 테이블의 자신의 정보를 $device_array에 저장 
            $select = "SELECT device_name,device_type,theme FROM $DB_table WHERE mac = \"$DB_mac\"";
            $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
            
            //OS 나레이션 출력 담당하는 ESP를 제외하고 실행
            if($device_array['device_type'] !== 'mp3'){
                $select = "UPDATE $DB_table SET shift_machine = 0 WHERE mac = \"$DB_mac\""; //shift_machine을 0으로 변경
                $result = mysqli_query($conn, $select); //쿼리 실행 

                /* 
                장치 종류에 따라 Mysql 테이블 이름이 달라 분리 해놓음
                ex) iotglove = ioglove_g1 
                ex) cyberpunk 테마의 duct = cyberpunk_duct
                */ 
                if($device_array['device_type'] == 'iotglove'){
                    $select = "SELECT * FROM {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." WHERE device_name = '{$device_array['device_name']}'";
                }
                else{
                    $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = '{$device_array['device_name']}'";
                }
                //웹 새로고침 요청 
                //DB에 변경사항 있을때마다 ESP에서 ReceiveMine을 호출하는데 이때 호출한 장치가 iotglove일 때 get /api/iotglove_php_request 호출
                if($device_array['device_type'] === 'iotglove'){
                    file_get_contents('http://localhost:5000/api/iotglove_php_request');
                }
                //DB에 변경사항 있을때마다 ESP에서 ReceiveMine을 호출하는데 이때 호출한 장치가 iotglove가 아닐 때
                else {
                    //iotglove가 아니면서 사이버펑크 테마일 때 
                    if($device_array['theme'] === 'cyberpunk'){
                        file_get_contents('http://localhost:5000/api/cyberpunk_php_request');
                    }
                    //쌈지길 추후 업데이트하는 황무지테마 
                    else if($device_array['theme'] === 'deadland'){
                        file_get_contents('http://localhost:5000/api/deadland_php_request');
                    }
                }
            } 
            break;
        case "Receive" : //이름이 입력된 장치의 정보를 가져오는 쿼리 
            //device 테이블의 가져오고싶은 장치의 정보를 $device_array에 저장 
            $select = "SELECT device_name,device_type,theme FROM $DB_table WHERE device_name = \"$DB_key\"";
            $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
            /* 
                원하는 장치 데이터 수신 쿼리 - 장치 종류에 따라 Mysql 테이블 이름이 달라 분리 해놓음
                ex) iotglove = ioglove_g1 
                ex) cyberpunk 테마의 duct = cyberpunk_duct
                */ 
            if($device_array['device_type'] == 'iotglove'){ //iotglove일 때
                $select = "SELECT * FROM {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." WHERE device_name = '{$device_array['device_name']}'";
            }
            else { //iotglove가 아닌 다른 장치일 때 
                $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = '{$device_array['device_name']}'";
            }
            break;
        case "ReceiveMP3" : //메인 OS 나레이션 정보를 가져오는 쿼리 (arduino.ide파일로 esp파일로 따로 짜여져있음.)
            //MP3는 shift_machine을 한번에 없애는게 아닌 순차적으로 -1하면서 제거하는 방식으로, 자신의 정보를 가져온다는 점은 ReceiveMine과 의미가 비슷하지만 분리해놓았음. 
            $select = "UPDATE $DB_table SET shift_machine = shift_machine-1 WHERE device_name = \"$DB_key\"";
            $result = mysqli_query($conn, $select);

            //device 테이블에서 cyberpunkOS정보를 $device_array에 저장 (나중에는 deadlandOS도 추가됨)
            $select = "SELECT device_name,device_type,theme FROM $DB_table WHERE device_name = \"$DB_key\"";
            $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));

            //cyberpunk_mp3 테이블에서 재생해야하는 트랙 번호(컬럼명: device_name)를 지정해 폴더 번호,파일 번호를 가져온다. 
            $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = $DB_value";
            break;
        case "Loop" :
            //shift_machine이 변경되었는지, watchdog(ESP강제 재시작명령)이 변경되었는지 확인하기 위한 쿼리 
            //ESP에서 정기적인 주기로 (1~2초) 계속 호출한다.
            $select = "SELECT shift_machine,watchdog FROM $DB_table WHERE mac = \"$DB_mac\"" ;
            break;
        default :
            break;
    }   
    //ESP에서 php로 전송하는 코드 - has2wifi.Send("G3P1"(장치명),"message_code"(컬럼명),"1"(value));
    if($DB_request == "Send"){ //입력하는 장치이름과 컬럼명의 value를 변경해준다. 
        //device 테이블에서 전송하려는 장치의 device 테이블에서의 정보를 $device_array에 저장 
        $select = "SELECT device_name,device_type,theme,shift_machine FROM $DB_table WHERE device_name = \"$DB_key\"";
        $device_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
        
        //iotglove일때와 그 외의 장치일때 iotglove_그룹 || 테마_장치명의 테이블안의 장치 정보를 $theme_array에 저장한다. 
        // ex) iotglove_g1 || cyberpunk_duct
        if($device_array['device_type']==='iotglove'){
            $select = "SELECT * FROM {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." WHERE device_name = \"$DB_key\"";
        }
        else{
            $select = "SELECT * FROM {$device_array['theme']}_{$device_array['device_type']} WHERE device_name = \"$DB_key\"";
        }
        $theme_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
        //send 데이터 쿼리 만들어 전송하는 작업 
        switch($DB_column){
            case "restart": //장치가 켜졌을때 restart = 1이고 켜진후 일정시간이 지났을때 restart = 0 (이 컬럼은 device 테이블에 존재함)
                $select = "UPDATE $DB_table SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "watchdog": //장치를 강제 재시작할때 watchdog = 1이고 켜진후 watchdog = 0 (이 컬럼은 device 테이블에 존재함 - 아직 사용하지 않는 테이블)
                $select = "UPDATE $DB_table SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "folder_num": //mp3 재생할 때 사용하는 컬럼. 폴더명을 변경함
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            case "file_num": //mp3 재생할 때 사용하는 컬럼. 파일명을 변경함
                $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = $DB_value WHERE device_name = '{$device_array['device_name']}'";
                mysqli_query($conn, $select);
                break;
            default : 
                //iot글러브일때
                if($device_array['device_type'] == 'iotglove'){
                    if($DB_column == 'device_state' || $DB_column == 'game_state'){ //ESP에서 device_state 혹은 game_state를 변경할 때 처리해주는 부분
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET $DB_column = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                    }
                    else if($DB_column == 'tagger_name'){ //게임 시작 후 3분 뒤 술래가 제단에 태그하면 게임을 하고있는 glove의 tagger_name이라는 컬럼에 제단에 태그한 술래의 이름을 넣어주는 역할 
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET $DB_column = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                    }
                    else if($DB_column == 'location'){ //술래위치가 변경되면 모든 플레이어와의 위치를 비교 & 생존자 위치가 변경되면 그 생존자와 술래의 위치만 비교 
                        //같은방이면 vibe=2, 옆방이면 vibe=1, 멀면 vibe=0 (진동세기 2:강함 1:약함 0:없음)

                        //변경된 iotglove 위치 변경 - ESP에서 자신의 위치가 어디에 있는지 알아서 보내줌. 
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET $DB_column = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                        mysqli_query($conn, $select);

                        //위치 변경된 iotglove가 술래(tagger)일 때
                        if($theme_array['role'] === 'tagger'){
                            for($i = 1;$i<=8;$i++){
                                $select = "SELECT device_name,role,location FROM {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." WHERE device_name= '".substr($device_array['device_name'],0,2)."P".$i."'";
                                $player_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
                                if($player_array['location'] == $DB_value){ //같은방일때 vibe=2
                                    $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET vibe = 2 WHERE device_name = '".substr($device_array['device_name'],0,2)."P".$i."'";
                                    mysqli_query($conn, $select);
                                }
                                else{ //같은방이 아닐때 진동 0 -> vibe=0
                                    $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET vibe = 0 WHERE device_name = '".substr($device_array['device_name'],0,2)."P".$i."'";
                                    mysqli_query($conn, $select);
                                }
                                $select = "UPDATE device SET shift_machine = 2 WHERE device_name = '".substr($device_array['device_name'],0,2)."P".$i."'";
                                    mysqli_query($conn, $select);
                            }
                        }
                        //위치 변경된 iotglove가 생존자(player)일 때
                        else{ //player
                            //술래 찾아내기 
                            $select = "SELECT device_name,role,location FROM {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." WHERE role = 'tagger'";
                            $player_array = mysqli_fetch_assoc(mysqli_query($conn, $select));
                            //같은방일때 vibe=2
                            if($player_array['location'] == $DB_value){ 
                                $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET vibe = 2 WHERE device_name = '{$theme_array['device_name']}'";
                                mysqli_query($conn, $select);
                            }
                            else{ //같은방이 아닐때 진동 0 -> vibe=0
                                $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET vibe = 0 WHERE device_name = '".substr($device_array['device_name'],0,2)."P".$i."'";
                                mysqli_query($conn, $select);
                            }
                        }
                    }
                    else if($DB_column == 'message_sender'){ //iotglove에 메시지를 보내는 기능이 있는데 메시지를 보내는 사람 이름을 목표 장치의 message_sender에 입력
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET $DB_column = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                        mysqli_query($conn, $select);
                    }
                    else if($DB_column == 'message_code'){ //iotglove에 메시지를 보내는 기능이 있는데 메시지의 코드를 목표 장치의 message_code에 입력
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET message_code = {$DB_value} WHERE device_name = '{$device_array['device_name']}'";
                        mysqli_query($conn, $select);
                    }
                    else if($DB_column == 'role'){ //iotglove 역할 변경
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET role = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                        mysqli_query($conn, $select);
                    }
                    else{//$DB_column이 위의 경우가 아닐때 (보통 life_chip, battery_pack, exp등 누적해서 더해야하는 경우의 컬럼)
                        $select = "UPDATE {$device_array['device_type']}_".substr($device_array['device_name'],0,2)." SET $DB_column = $DB_column + $DB_value WHERE device_name = '{$device_array['device_name']}'";
                    }
                }
                //iotglove가 아닐때
                else{ 
                    if($DB_column == 'device_state' || $DB_column == 'game_state'){ //ESP에서 device_state 혹은 game_state를 변경할 때 처리해주는 부분
                        $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                        mysqli_query($conn, $select);
                        if($device_array['device_type'] === 'temple' && $DB_value === 'activate'){
                            $select = "UPDATE {$device_array['theme']}_revivalmachine SET device_state = 'ready_activate' WHERE device_state = 'ready'";
                            mysqli_query($conn, $select);
                            $select = "UPDATE $DB_table SET shift_machine = {$device_array['shift_machine']} + 1 WHERE device_type = 'revivalmachine'";
                        }
                    }
                    else if($DB_column == 'tag_player'){ //덕트에서 tag_player를 send할 때 
                        $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = '{$DB_value}' WHERE device_name = '{$device_array['device_name']}'";
                    }
                    else{//life_chip, battery_pack, exp등 누적해서 더해야하는 경우의 컬럼
                        $select = "UPDATE {$device_array['theme']}_{$device_array['device_type']} SET $DB_column = $DB_column + $DB_value WHERE device_name = '{$device_array['device_name']}'";
                    }
                }
                mysqli_query($conn, $select);
                break;
        }
        //shift_machine +1 해주는 부분. mp3를 제외하고 모든 send에서 send를 받을때마다 항상 shift_machine을 +1 해주기
        if($device_array['device_type'] !== mp3){
            $select = "UPDATE $DB_table SET shift_machine = {$device_array['shift_machine']} + 1 WHERE device_name = '{$device_array['device_name']}'";
        }
        //DB변경될 때마다 웹 새로고침 요청 
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

    if($DB_request != "Send"){ //send일때를 제외하고 json형식의 데이터를 ESP쪽으로 전송해준다. 
        while($row = mysqli_fetch_assoc($result)){
            $json_DB = json_encode($row);
            echo $json_DB;
        }
    }


    mysqli_close($conn);
?>