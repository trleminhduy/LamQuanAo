$(document).ready(function() {
    // Load tỉnh
    $.get('/api/ghn/provinces', function(res) {
        if (res.code === 200) {
            res.data.forEach(p => {
                $('#province').append(`<option value="${p.ProvinceName}" data-id="${p.ProvinceID}">${p.ProvinceName}</option>`);
            });
        }
    });
    
    // Chọn tỉnh -> load quận
    $('#province').change(function() {
        const id = $(this).find(':selected').data('id');
        $('#district').prop('disabled', false).html('<option value="">Chọn quận/huyện</option>');
        $('#ward').prop('disabled', true).html('<option value="">Chọn phường/xã</option>');
        
        $.post('/api/ghn/districts', { province_id: id }, function(res) {
            if (res.code === 200) {
                res.data.forEach(d => {
                    $('#district').append(`<option value="${d.DistrictName}" data-id="${d.DistrictID}">${d.DistrictName}</option>`);
                });
            }
        });
    });
    
    // Chọn quận -> load phường
    $('#district').change(function() {
        const id = $(this).find(':selected').data('id');
        $('#district_id').val(id);
        $('#ward').prop('disabled', false).html('<option value="">Chọn phường/xã</option>');
        
        $.post('/api/ghn/wards', { district_id: id }, function(res) {
            if (res.code === 200) {
                res.data.forEach(w => {
                    $('#ward').append(`<option value="${w.WardName}" data-code="${w.WardCode}">${w.WardName}</option>`);
                });
            }
        });
    });
    
    // Chọn phường -> lưu ward_code
    $('#ward').change(function() {
        $('#ward_code').val($(this).find(':selected').data('code'));
    });
});