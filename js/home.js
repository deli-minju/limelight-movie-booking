// 슬라이드 버튼 기능
function slide(trackId, direction) {
    const track = document.getElementById('track-' + trackId);
    // 검색 페이지 등 슬라이더가 없는 경우 에러 방지
    if (!track) return;

    const scrollAmount = 220;
    
    if (direction === 1) {
        track.scrollLeft += scrollAmount;
    } else {
        track.scrollLeft -= scrollAmount;
    }
}

// 마우스 드래그 스크롤 & 클릭 구분
const sliders = document.querySelectorAll('.slider-track');
let isDown = false;
let startX;
let scrollLeft;
let isDragging = false;

// 슬라이더가 존재하는 페이지에서만 실행
if (sliders.length > 0) {
    sliders.forEach(slider => {
        // 이미지 드래그 방지
        const images = slider.querySelectorAll('img');
        images.forEach(img => {
            img.addEventListener('dragstart', (e) => e.preventDefault());
        });

        // 마우스 눌렀을 때
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        // 마우스 벗어났을 때
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        // 마우스 뗐을 때
        slider.addEventListener('mouseup', (e) => {
            isDown = false;
            slider.classList.remove('active');
            isDragging = false; 
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1; 
            
            // 5픽셀 이상 움직였을 때만 드래그로 간주
            if (Math.abs(walk) > 5) {
                isDragging = true; 
                slider.scrollLeft = scrollLeft - walk;
            }
        });

        slider.addEventListener('click', (e) => {
            if (isDragging) {
                e.preventDefault();
                e.stopPropagation();
                isDragging = false; 
            }
        });
    });
}

function toggleLike(btn, movieId) {
    if(event) event.stopPropagation();

    fetch('api/like_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ movie_id: movieId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.action === 'liked') btn.classList.add('active');
            else btn.classList.remove('active');
            
            setTimeout(() => { location.reload(); }, 300); 

        } else if (data.status === 'not_logged_in') {
            if(confirm('로그인이 필요한 서비스입니다.\n로그인 페이지로 이동하시겠습니까?')) {
                location.href = 'login.php';
            }
        } else {
            alert('오류: ' + (data.message || '알 수 없음'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

let currentMovieId = null;

function openReviewModal(movieId, title) {
    currentMovieId = movieId;
    
    document.getElementById('modal-movie-title').innerText = title;
    document.getElementById('review-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; 
    
    document.getElementById('review-text').value = '';
    
    loadReviews(movieId);
}

function closeReviewModal() {
    document.getElementById('review-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentMovieId = null;
}

function loadReviews(movieId) {
    const listContainer = document.getElementById('review-list-container');
    listContainer.innerHTML = '<div style="color:#777; text-align:center; padding:20px;">로딩중...</div>';

    fetch(`api/review_process.php?mode=list&movie_id=${movieId}`)
        .then(res => res.json())
        .then(data => {
            listContainer.innerHTML = '';
            
            if (data.length === 0) {
                listContainer.innerHTML = '<div style="color:#555; text-align:center; padding:20px;">첫 번째 한줄평을 남겨보세요!</div>';
                return;
            }

            data.forEach(review => {
                const item = document.createElement('div');
                item.className = 'review-item';
                item.innerHTML = `
                    <div class="review-meta">
                        <span class="review-user">${review.nickname}</span>
                        <span>${review.created_at}</span>
                    </div>
                    <div class="review-content" onclick="this.classList.toggle('expanded')" title="클릭해서 전체보기">
                        ${review.content}
                    </div>
                `;
                listContainer.appendChild(item);
            });
        })
        .catch(err => console.error(err));
}

function submitReview() {
    const text = document.getElementById('review-text').value.trim();
    
    if (!text) {
        alert('내용을 입력해주세요.');
        return;
    }

    const data = {
        mode: 'write',
        movie_id: currentMovieId,
        content: text
    };

    fetch('api/review_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('review-text').value = '';
            loadReviews(currentMovieId);
        } else if (data.status === 'not_logged_in') {
            if(confirm('로그인이 필요합니다. 로그인 하시겠습니까?')) {
                location.href = 'login.php';
            }
        } else {
            alert('등록 실패: ' + data.message);
        }
    })
    .catch(err => console.error(err));
}