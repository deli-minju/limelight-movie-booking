<div align="center">
  <img src="https://github.com/user-attachments/assets/b6ef8cd5-0798-4fc0-b22a-6b13a9c9c315" width="500" alt="limelight_logo">
</div>

# 🎬 LimeLight

<div align="center">

![License](https://img.shields.io/badge/license-MIT-green) ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)
<br>
[![Android Repository](https://img.shields.io/badge/Link-Android_App_Repository-3DDC84?style=flat&logo=android&logoColor=white)](https://github.com/deli-minju/limelight-android-app)

> **"영화로 일상을 비추다"**
>
> **Web & App 크로스 플랫폼 영화 예매 서비스**

</div>

---

## 📖 프로젝트 개요

**limelight**는 19세기 극장에서 무대 위 주인공을 강조하는 스포트라이트 용도로 널리 쓰였습니다. 이 조명 장치는 사라졌지만, "in the limelight"라는 표현은 그대로 남아 오늘날 "각광받다"라는 관용구로 굳어졌습니다.

영화 예매 서비스 **LimeLight**는 서비스명의 의미와 어원을 시각적으로 전달하기 위해 브랜드 컬러를 **Neon Lime**으로 정의했습니다. 또한 영화관의 어두운 조명 환경을 고려하여 다크 모드 UI를 채택했습니다.

본 리포지토리는 LimeLight 서비스의 웹 프론트엔드 및 백엔드 API 소스 코드를 포함하고 있습니다. 안드로이드 앱과 하나의 MySQL 데이터베이스를 공유하여 유기적인 서비스를 제공하는 **1인 풀스택 개발 프로젝트**입니다.

* **웹 서비스 배포 링크:** [http://devmanjoo.mycafe24.com/](http://devmanjoo.mycafe24.com/)
* **Android App 리포지토리:** [https://github.com/deli-minju/limelight-android-app](https://github.com/deli-minju/limelight-android-app)
* **프로젝트 기간:** 2025.11.05 ~ 2025.12.10
* **개발 인원:** 1인 (Design, Frontend, Backend, Android, DB)

---

## ✨ 핵심 기능

### 🖥️ 웹 서비스 & RESTful API
이 프로젝트는 사용자에게 영화 예매 웹 서비스를 제공함과 동시에, 안드로이드 앱이 데이터를 사용할 수 있도록 RESTful API 서버의 역할을 수행합니다.

### 📱 사용자 기능 - Web & App 공통

* **통합 계정:** 앱에서 가입한 계정으로 웹 로그인 가능

| 로그인 | 회원가입 |
| :---: | :---: |
| <img src="https://github.com/user-attachments/assets/4b296032-e0ec-47d1-877e-f5cd6f22be5c" width="100%"> | <img src="https://github.com/user-attachments/assets/81a3c8c2-8b65-4290-adfe-ec8cb9146021" width="100%"> |

<br>

* **영화 예매:** 극장 > 날짜 > 영화 > 시간 > 인원 선택 프로세스

| 예매 | 예매내역 |
| :---: | :---: |
| <img src="https://github.com/user-attachments/assets/a291ad0a-c17f-49ad-93d7-d96a77ece982" width="100%"> | <img src="https://github.com/user-attachments/assets/7109f533-c817-4234-9cfd-a152f67f4da2" width="100%"> |

<br>

* **무비차트 및 검색:** 현재 상영작/상영 예정작 조회 및 영화 검색 기능

| 홈 | 검색 |
| :---: | :---: |
| <img src="https://github.com/user-attachments/assets/44ee01ab-0938-4307-bb94-a25386dcbd2d" width="100%"> | <img src="https://github.com/user-attachments/assets/24b921fd-98f6-4bfa-b79d-4f1ce17b3418" width="100%"> |

<br>

* **게이미피케이션:** 관람 횟수에 따른 레벨 업 시스템 및 통계 제공

| 프로필 |
| :---: |
| <img src="https://github.com/user-attachments/assets/335bfcb1-ffd8-4dbb-82e1-5cbf1a720de9" width="100%"> |

<br>

* **커뮤니티:** 한줄평 작성 및 'My List(찜하기)' 기능

| 한줄평 | My List |
| :---: | :---: |
| <img src="https://github.com/user-attachments/assets/e1ceaaf1-3a37-4f85-8588-baf543a29074" width="100%"> | <img src="https://github.com/user-attachments/assets/83094ad1-9174-43d8-8bff-a5d094fe605b" width="100%"> |

<br>

* **결제 시스템:** 조조, 청소년, 우대 등 조건별 요금 자동 계산

| 요금 자동 계산 |
| :---: |
| <img src="https://github.com/user-attachments/assets/71a4a91e-a656-4639-bb64-051558b64f67" width="100%"> |

### 🔐 관리자 기능 - Web Only
* **대시보드:** 영화, 지점, 상영 스케줄 데이터의 통합 관리
* **데이터 보호:** 영화나 지점 삭제 시 DB에서 완전히 지우지 않고 `is_deleted` 플래그를 사용하여 기존 고객의 예매 내역을 안전하게 보존
* **스케줄링:** 날짜별 상영 시간표 등록 및 삭제 시스템

| 관리자 대시보드 |
| :---: |
| <img src="https://github.com/user-attachments/assets/92001a01-7e83-448f-8775-77114e6b0f62" width="100%"> |

---

## 🛠️ 개발 환경

### **Web & Backend**
* **Server:** PHP 8.2
* **Database:** MySQL (MariaDB 10.x)
* **Frontend:** HTML5, CSS3, JavaScript
* **Hosting:** Cafe24 (SFTP 배포)

### **Android App**
* **Role:** Client Application
* **Framework:** Native Android (Java)
* **Library:** Retrofit2

---

## 🔒 보안 및 아키텍처

* **API Security:**
    * `API Key` 인증 방식을 도입하여 인가되지 않은 외부 접근 차단
* **Database Security:**
    * `mysqli_real_escape_string`을 통한 SQL Injection 방지
    * 사용자 비밀번호 Hashing 저장
* **Config Management:**
    * `db_secret.php` 분리 및 접근 제한을 통한 DB 접속 정보 보호

---

## 📂 데이터베이스 설계

주요 테이블 구성:
* `users`: 사용자 정보
* `movies`: 영화 상세 정보
* `theaters`: 극장 지점 정보
* `showtimes`: 상영 스케줄
* `bookings`: 예매 내역
* `reviews`: 한줄평
* `wishlist`: 찜한 영화 목록

---

## 📝 라이선스

이 프로젝트는 MIT License를 따릅니다.
자세한 내용은 `LICENSE` 파일을 참고하세요.

Copyright (c) 2025 Minju Kim
