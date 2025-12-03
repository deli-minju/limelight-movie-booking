let selTheater = null;
let selDate = null;
let selMovie = null;
let selTime = null;
let isMorning = false; // 조조 여부

// 극장 선택
function selectTheater(btn, id) {
    resetSelection('theater');
    
    document.querySelectorAll('.theater-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    
    selTheater = id;
    
    // 날짜가 이미 선택되어 있다면 영화 목록 로드
    if (selDate) loadMovies();
}

// 날짜 선택
function selectDate(btn, dateStr) {
    resetSelection('date');

    document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    selDate = dateStr;

    // 극장이 이미 선택되어 있다면 영화 목록 로드
    if (selTheater) loadMovies();
}

// 영화 선택
function selectMovie(btn, id) {
    resetSelection('movie');
    
    document.querySelectorAll('.movie-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    
    selMovie = id;
    loadTimes(); // 시간표 로드
}

// 시간 선택
function selectTime(btn, id, morningFlag) {
    document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    
    selTime = id;
    isMorning = morningFlag; // 조조 여부 저장
    
    calcPrice(); // 가격 재계산
}

// 영화 목록 불러오기
function loadMovies() {
    const container = document.getElementById('movie-list');
    container.innerHTML = '<div class="loading">로딩중...</div>';

    fetch(`api/get_booking_options.php?type=movies&theater=${selTheater}&date=${selDate}`)
        .then(res => res.json())
        .then(data => {
            container.innerHTML = '';
            if (data.length === 0) {
                container.innerHTML = '<div class="empty-msg">상영 정보가 없습니다.</div>';
                return;
            }
            
            data.forEach(movie => {
                const btn = document.createElement('button');
                btn.className = 'select-btn movie-btn';
                btn.innerHTML = `<img src="${movie.poster_img}" class="movie-thumb"><span>${movie.title}</span>`;
                btn.onclick = () => selectMovie(btn, movie.id);
                container.appendChild(btn);
            });
        });
}

// 시간표 불러오기
function loadTimes() {
    const container = document.getElementById('time-list');
    container.innerHTML = '<div class="loading">로딩중...</div>';

    // 캐시 방지용 타임스탬프
    fetch(`api/get_booking_options.php?type=times&theater=${selTheater}&date=${selDate}&movie=${selMovie}&t=${new Date().getTime()}`)
        .then(res => res.json())
        .then(data => {
            container.innerHTML = '';
            if (data.length === 0) {
                container.innerHTML = '<div class="empty-msg">상영 시간이 없습니다.</div>';
                return;
            }

            data.forEach(time => {
                const hour = parseInt(time.start_time.split(' ')[1].substring(0, 2));
                const isMorningTime = (hour >= 7 && hour < 11);
                
                const badge = isMorningTime ? '<span class="badge-morning">조조</span>' : '';
                
                const btn = document.createElement('button');
                
                // 시간이 지났으면 비활성화 처리
                if (time.is_past) {
                    btn.className = 'select-btn time-btn disabled'; 
                    btn.disabled = true; // 클릭 방지
                    btn.innerHTML = `${badge}${time.time_display}<br><small>마감</small>`;
                } else {
                    btn.className = 'select-btn time-btn';
                    btn.innerHTML = `${badge}${time.time_display}<br><small>${time.screen_name}</small>`;
                    btn.onclick = () => selectTime(btn, time.id, isMorningTime);
                }
                
                container.appendChild(btn);
            });
        });
}

// 가격 계산
function calcPrice() {
    // 입력값이 비어있으면 0으로 취급
    const getVal = (id) => {
        const val = document.getElementById(id).value;
        return val === "" ? 0 : parseInt(val);
    };
    
    const cntAdult = getVal('cnt-adult');
    const cntTeen = getVal('cnt-teen');
    const cntPref = getVal('cnt-pref');
    const cntSenior = getVal('cnt-senior');

    const totalPeople = cntAdult + cntTeen + cntPref + cntSenior;

    // 최대 인원 8명 제한
    if (totalPeople > 8) {
        alert('최대 8명까지만 예매 가능합니다.');
        // 현재 이벤트를 발생시킨 입력창 초기화
        if(event && event.target) event.target.value = ""; 
        calcPrice(); // 재귀 호출로 다시 계산
        return;
    }

    // 가격표 정의
    let priceAdult = 15000;
    let priceTeen = 12000;
    let pricePref = 5000;
    let priceSenior = 7000;

    // 조조 할인 적용 (일반, 청소년만 -4000원)
    if (isMorning) {
        priceAdult -= 4000;
        priceTeen -= 4000;
        // 우대, 경로는 중복 할인 불가
    }

    const total = (cntAdult * priceAdult) + (cntTeen * priceTeen) + (cntPref * pricePref) + (cntSenior * priceSenior);
    
    // 3자리 콤마
    document.getElementById('total-price').innerText = total.toLocaleString() + "원";
}

// 상위 항목 변경 시 하위 항목 리셋
function resetSelection(level) {
    // 극장이나 날짜를 바꾸면 -> 영화 이하 초기화
    if (level === 'theater' || level === 'date') {
        selMovie = null;
        document.getElementById('movie-list').innerHTML = '<div class="empty-msg">극장과 날짜를 먼저 선택해주세요.</div>';
        resetSelection('movie'); // 연쇄 호출
    }
    
    // 영화를 바꾸면 -> 시간, 인원 초기화
    if (level === 'movie') {
        selTime = null;
        isMorning = false;
        document.getElementById('time-list').innerHTML = '<div class="empty-msg">영화를 선택해주세요.</div>';
        
        // 인원 입력창 초기화
        document.querySelectorAll('input[type=number]').forEach(i => i.value = "");
        document.getElementById('total-price').innerText = "0원";
    }
}

function submitBooking() {
    if (!selTime) {
        alert('상영 시간을 선택해주세요.');
        return;
    }
    
    // "14,000원" -> 14000 숫자만 추출
    const totalStr = document.getElementById('total-price').innerText.replace(/[^0-9]/g, '');
    const totalPrice = parseInt(totalStr);

    if (totalPrice === 0) {
        alert('인원을 선택해주세요.');
        return;
    }

    // 전송할 데이터 준비
    const getVal = (id) => document.getElementById(id).value === "" ? 0 : document.getElementById(id).value;

    const data = {
        showtime_id: selTime,
        adult: getVal('cnt-adult'),
        teen: getVal('cnt-teen'),
        pref: getVal('cnt-pref'),
        senior: getVal('cnt-senior'),
        total_price: totalPrice
    };

    if(!confirm(`${totalPrice.toLocaleString()}원 결제하시겠습니까?`)) return;

    // 서버로 전송
    fetch('api/booking_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('예매가 완료되었습니다!');
            window.location.replace('booking_list.php'); 
        } else {
            alert('예매 실패: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('서버 통신 중 오류가 발생했습니다.');
    });
}