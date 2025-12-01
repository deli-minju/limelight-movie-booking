document.addEventListener('DOMContentLoaded', function() {
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

    const emailInput = document.querySelector('input[name="email"]');
    
    emailInput.addEventListener('input', function() {
        const emailPattern = /^[A-Za-z0-9_\.\-]+@[A-Za-z0-9\-]+\.[A-Za-z0-9\-]+/;
        
        if (this.value.length > 0 && !emailPattern.test(this.value)) {
            this.classList.add('error');
        } else {
            this.classList.remove('error');
        }
    });

    const birthInputs = document.querySelectorAll('.birth-input');
    const birthContainer = document.querySelector('.birth-container');

    birthInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        input.addEventListener('blur', function() {
            let isEmpty = false;
            birthInputs.forEach(bi => {
                if(bi.value === '') isEmpty = true;
            });

            if(isEmpty) {
                birthContainer.classList.add('error');
            } else {
                birthContainer.classList.remove('error');
            }
        });
    });
});

function validateForm() {
    const pwInput = document.getElementById('password');
    const pwConfirm = document.getElementById('password_confirm');

    if (pwInput.value !== pwConfirm.value) {
        alert('비밀번호가 일치하지 않습니다.');
        pwConfirm.focus();
        return false;
    }
    return true;
}

function checkId() {
    const val = document.getElementById('userid').value;
    if(!val) alert('아이디를 입력해주세요.');
    else alert('사용 가능한 아이디입니다.');
}

function checkNickname() {
    const val = document.getElementById('nickname').value;
    if(!val) alert('닉네임을 입력해주세요.');
    else alert('사용 가능한 닉네임입니다.');
}