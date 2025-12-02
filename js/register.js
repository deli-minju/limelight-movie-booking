let isIdChecked = false;
let isNicknameChecked = false;

document.addEventListener('DOMContentLoaded', function() {
    // 입력창 내용을 바꾸면 중복 확인 초기화
    const userIdInput = document.getElementById('userid');
    const nicknameInput = document.getElementById('nickname');

    userIdInput.addEventListener('input', function() {
        isIdChecked = false;
        this.style.borderColor = ""; 
    });

    nicknameInput.addEventListener('input', function() {
        isNicknameChecked = false;
        this.style.borderColor = "";
    });

    // 비밀번호 실시간 확인
    const pwInput = document.getElementById('password');
    const pwConfirm = document.getElementById('password_confirm');
    const pwMsg = document.getElementById('pw-msg');

    pwConfirm.addEventListener('input', function() {
        if (pwInput.value === "") return;

        if (pwInput.value === this.value) {
            this.style.borderColor = "#CFFF04";
            pwMsg.innerText = "비밀번호가 일치합니다.";
            pwMsg.style.color = "#CFFF04";
        } else {
            this.style.borderColor = "#F33F3F";
            pwMsg.innerText = "비밀번호가 일치하지 않습니다.";
            pwMsg.style.color = "#F33F3F";
        }
    });

    // 이메일 형식 검사
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('input', function() {
        const emailPattern = /^[A-Za-z0-9_\.\-]+@[A-Za-z0-9\-]+\.[A-Za-z0-9\-]+/;
        if (this.value.length > 0 && !emailPattern.test(this.value)) {
            this.classList.add('error');
        } else {
            this.classList.remove('error');
        }
    });

    // 생년월일 숫자만 입력
    const birthInputs = document.querySelectorAll('.birth-input');
    const birthContainer = document.getElementById('birth-box');

    birthInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        input.addEventListener('blur', function() {
            let isEmpty = false;
            birthInputs.forEach(bi => { if(bi.value === '') isEmpty = true; });
            if(isEmpty) birthContainer.classList.add('error');
            else birthContainer.classList.remove('error');
        });
    });
});

// 폼 제출 전 검사
function validateForm() {
    const pwInput = document.getElementById('password');
    const pwConfirm = document.getElementById('password_confirm');
    const emailInput = document.getElementById('email');

    if (!isIdChecked) {
        alert('아이디 중복 확인을 해주세요.');
        document.getElementById('userid').focus();
        return false;
    }
    if (!isNicknameChecked) {
        alert('닉네임 중복 확인을 해주세요.');
        document.getElementById('nickname').focus();
        return false;
    }

    if (pwInput.value !== pwConfirm.value) {
        alert('비밀번호가 일치하지 않습니다.');
        pwConfirm.focus();
        return false;
    }

    if (emailInput.classList.contains('error')) {
        alert('올바른 이메일 형식을 입력해주세요.');
        emailInput.focus();
        return false;
    }

    return true;
}

// 아이디 중복 확인 (캐시 방지 적용)
function checkId() {
    const input = document.getElementById('userid');
    const val = input.value.trim();
    
    if(!val) {
        alert('아이디를 입력해주세요.');
        return;
    }

    fetch('check_duplicate.php?type=userid&value=' + val + '&t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            if (data.status === 'duplicate') {
                alert('이미 사용 중인 아이디입니다.');
                input.value = "";
                input.focus();
                isIdChecked = false;
                input.style.borderColor = "#F33F3F";
            } else if (data.status === 'available') {
                alert('사용 가능한 아이디입니다.');
                isIdChecked = true;
                input.style.borderColor = "#CFFF04";
            } else {
                alert('오류: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// 닉네임 중복 확인 (캐시 방지 적용)
function checkNickname() {
    const input = document.getElementById('nickname');
    const val = input.value.trim();
    
    if(!val) {
        alert('닉네임을 입력해주세요.');
        return;
    }

    fetch('check_duplicate.php?type=nickname&value=' + val + '&t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            if (data.status === 'duplicate') {
                alert('이미 사용 중인 닉네임입니다.');
                input.value = "";
                input.focus();
                isNicknameChecked = false;
                input.style.borderColor = "#F33F3F";
            } else if (data.status === 'available') {
                alert('사용 가능한 닉네임입니다.');
                isNicknameChecked = true;
                input.style.borderColor = "#CFFF04";
            }
        })
        .catch(error => console.error('Error:', error));
}