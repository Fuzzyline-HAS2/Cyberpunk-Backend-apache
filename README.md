# 하이드앤시크 쌈지길점 - 백엔드 Apache

### 🛠︎Tools🛠︎
<img src="https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=black"/> <img src="https://img.shields.io/badge/Apache-D22128?style=flat-square&logo=apache&logoColor=black"/>

##
## 목차
1. [gitbash를 이용해 최신 버전 pull하기](#gitbash를-이용해-최신-버전-pull하기)
2. [실행](#실행)
3. [업데이트](#업데이트)
4. [문제 상황](#문제-상황)
5. [DB 업데이트 명령어 정리](#db-업데이트-명령어-정리)
    - [device](#device)
    - [itembox](#itembox)
    - [revivalmachine](#revivalmachine)
    - [tagmachine](#tagmachine)
    - [duct](#duct-vent)
    - [generator](#generator)
    - [escapemachine](#escapemachine)
    - [temple](#temple)

### 실행 방법

## gitbash를 이용해 최신 버전 pull하기
<a href = "https://git-scm.com/downloads">gitbash</a>가 설치되어있지 않다면?

```
$ git fetch #하고 이상없으면
$ git pull origin main
```

## 실행

보통은 컴퓨터를 켰을 때 자동으로 실행됨.

## 업데이트
1. 코드를 작성한다.
2. 작성한 코드를 깃허브에 업로드한다.
```
$ git diff #파일의 변경된 부분을 확인한 뒤
$ git add . #파일의 변경된 부분 전부 추가
$ git commit -m "커밋 내용" 
$ git push origin main
```

## 문제 상황 
### 1. pull이 안된다.
fetch를 한 뒤 github 저장소와 다르면 
```
$ git restore
```
restore를 통해 로컬 변경상황을 제거한 뒤 pull을 해주면 된다.
(로컬에서 작성한 변경사항은 삭제된다. -> 항상 파일을 최신으로 업데이트 해놓고 추가사항 코딩한 뒤 push할것)
### 2. 아파치 서버가 안켜진것 같다. 
만약에 실행이 안되면 C:\Apache24\bin 위치에서 ApacheMonitor.exe 실행시키기.

## DB 업데이트 명령어 정리
[MySQL] has2 스키마

‼ manage_state는 받고 적용한 뒤 장치가 정상적인 작동을 하면 비워주기  
‼ 장치 오류나면 device_state에 오류난 장치명 입력하기 (ex.RFID) 

### device

  명령어 | 내용 |자료형| 색 
  :-----: | :-----: | :-----: | :-----:
  `restart`| **재시작 되었음을 1로 표시/ 정해진 시간 뒤 0으로 변경** | Int | X
  `watchdog` | **ESP를 리셋. setup에서 0으로 변경** | Int | X
  
### itembox

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `used` |**사용완료**| VARCHAR(String) | 파란색
    `repaired_all` |**전체수리완료**| VARCHAR(String) | 파란색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `mu` |**강제사용완료**| VARCHAR(String) | 파란색
    
 ### revivalmachine

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `open`|**상자열기**| VARCHAR(String) | 하늘색
    `used` |**상자사용완료**| VARCHAR(String) | 파란색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `mo`|**강제상자열기**| VARCHAR(String) | 하늘색
    `mu` |**강제사용완료**| VARCHAR(String) | 파란색
    
 ### tagmachine

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `open`|**도어오픈**| VARCHAR(String) | 하늘색
    `lock` |**도어잠금**| VARCHAR(String) | 초록색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `mo`|**강제도어오픈**| VARCHAR(String) | 하늘색
    `ml` |**강제도어잠금**| VARCHAR(String) | 초록색

### duct-vent 

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `open`|**덕트오픈**| VARCHAR(String) | 하늘색
    `lock` |**덕트잠금**| VARCHAR(String) | 초록색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `mo`|**강제덕트오픈**| VARCHAR(String) | 하늘색
    `ml` |**강제덕트잠금**| VARCHAR(String) | 초록색
    
 ### generator

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `battery_max`|**배터리공급완료**| VARCHAR(String) | 초록색
    `starter_finish` |**스타터완료**| VARCHAR(String) | 하늘색
    `repaired`|**수리완료**| VARCHAR(String) | 파란색
    `repaired_all` |**전체수리완료**| VARCHAR(String) | 파란색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `mbm`|**강제배터리공급완료**| VARCHAR(String) | 초록색
    `msf` |**강제스타터완료**| VARCHAR(String) | 하늘색
    `mr`|**강제수리완료**| VARCHAR(String) | 파란색
    `mra` |**강제전체수리완료**| VARCHAR(String) | 파란색
    
 ### escapemachine 

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `escape`|**탈출완료**| VARCHAR(String) | 초록색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `me`|**강제탈출완료**| VARCHAR(String) | 초록색
    
 ### temple

  - game_state
  
    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색

  - device_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `setting`|**네오픽셀 흰색, setting모드**| VARCHAR(String) | 흰색
    `ready` |**네오픽셀 빨간색, ready모드**| VARCHAR(String) | 빨간색
    `activate` |**네오픽셀 노란색, activate모드**| VARCHAR(String) | 노란색
    `takenchip_max`|**술래승리**| VARCHAR(String) | 보라색
    `player_win` |**생존자승리**| VARCHAR(String) | 초록색
    `player_lose` |**생존자패배**| VARCHAR(String) | 보라색
    
  - manage_state

    명령어 | 내용 |자료형| 색 
    :-----: | :-----: | :-----: | :-----:
    `mtm`|**강제술래승리**| VARCHAR(String) | 보라색

작성자 : 안혜수
