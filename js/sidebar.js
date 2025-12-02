let isEditMode = false;

function toggleEditMode() {
    const body = document.body;
    const listArea = document.getElementById('my-lists-area');
    const editBtn = document.getElementById('btn-edit-list');
    const btnText = editBtn.querySelector('.btn-text');

    isEditMode = !isEditMode;

    if (isEditMode) {
        body.classList.add('is-editing');
        body.classList.add('no-scroll'); 
        
        listArea.classList.add('edit-mode');

        btnText.innerText = "완료";
        editBtn.classList.add('done');
        
    } else {
        body.classList.remove('is-editing');
        body.classList.remove('no-scroll'); 
        
        listArea.classList.remove('edit-mode');

        btnText.innerText = "리스트 편집";
        editBtn.classList.remove('done');
    }
}

// 항목 삭제 및 메인 화면 동기화
function deleteWishItem(movieId, btnElement) {
    if (!confirm('정말 삭제하시겠습니까?')) {
        return;
    }

    // 서버에 삭제 요청 보내기
    fetch('api/delete_wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ movie_id: movieId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const li = btnElement.closest('li');
            li.remove();
            
            const ul = document.getElementById('wishlist-ul');
            if (ul.children.length === 0) {
                ul.innerHTML = '<li class="list-item empty-msg" style="color: #777; font-size: 13px; justify-content: center;">아직 찜한 영화가 없어요.</li>';
            }

            const mainHeartBtn = document.querySelector(`.btn-like[onclick*="toggleLike(this, ${movieId})"]`);
            
            if (mainHeartBtn) {
                mainHeartBtn.classList.remove('active');
            }

        } else {
            alert('삭제 실패: ' + (data.message || '알 수 없는 오류'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('서버 통신 중 오류가 발생했습니다.');
    });
}