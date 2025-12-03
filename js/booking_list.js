function cancelBooking(bookingId) {
    if (!confirm('정말 예매를 취소하시겠습니까?\n(취소 후 복구할 수 없습니다)')) {
        return;
    }

    fetch('api/cancel_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('예매가 정상적으로 취소되었습니다.');
            location.reload(); // 새로고침하여 목록 갱신
        } else {
            alert('취소 실패: ' + (data.message || '오류 발생'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('서버 통신 중 오류가 발생했습니다.');
    });
}