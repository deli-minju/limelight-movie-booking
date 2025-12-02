function slide(trackId, direction) {
    const track = document.getElementById('track-' + trackId);
    const scrollAmount = 220;
    
    if (direction === 1) {
        track.scrollLeft += scrollAmount;
    } else {
        track.scrollLeft -= scrollAmount;
    }
}

// 마우스 드래그 스크롤
const sliders = document.querySelectorAll('.slider-track');
let isDown = false;
let startX;
let scrollLeft;

sliders.forEach(slider => {
    // 포스터 이미지를 잡고 끌 때 고스트 이미지가 생겨서 스크롤이 끊기는 것을 막음
    const images = slider.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('dragstart', (e) => e.preventDefault());
    });

    // 마우스를 눌렀을 때
    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.classList.add('active');
        slider.style.scrollBehavior = 'auto';
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    // 마우스가 영역을 벗어났을 때
    slider.addEventListener('mouseleave', () => {
        isDown = false;
        slider.classList.remove('active');
        slider.style.scrollBehavior = 'smooth';
    });

    // 마우스를 뗐을 때
    slider.addEventListener('mouseup', () => {
        isDown = false;
        slider.classList.remove('active');
        slider.style.scrollBehavior = 'smooth';
    });

    // 마우스를 움직일 때
    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        
        const walk = (x - startX) * 1; 
        
        slider.scrollLeft = scrollLeft - walk;
    });
});

function toggleLike(btn, movieId) {
    fetch('api/like_process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ movie_id: movieId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.action === 'liked') {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
            
            setTimeout(() => {
                location.reload(); 
            }, 300); 

        } else if (data.status === 'not_logged_in') {
            if(confirm('로그인이 필요한 서비스입니다.\n로그인 페이지로 이동하시겠습니까?')) {
                location.href = 'login.php';
            }
        } else {
            alert('오류가 발생했습니다: ' + (data.message || '알 수 없음'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('서버 통신 오류입니다.');
    });
}