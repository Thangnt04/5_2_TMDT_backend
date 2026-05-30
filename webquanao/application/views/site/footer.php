<style>
.site-footer {
    background-color: #f8f9fa;
    padding: 50px 0 20px;
    border-top: 1px solid #e7e7e7;
    margin-top: 40px;
    font-family: Arial, sans-serif;
}
.site-footer h4 {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
    text-transform: uppercase;
}
.site-footer ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.site-footer ul li {
    margin-bottom: 12px;
}
.site-footer ul li a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s;
    font-size: 14px;
}
.site-footer ul li a:hover {
    color: #007bff;
}
.footer-contact address {
    color: #666;
    line-height: 1.8;
    font-size: 14px;
    margin-bottom: 15px;
}
.footer-contact .glyphicon {
    margin-right: 8px;
    color: #007bff;
}
.footer-social a {
    display: inline-block;
    margin-right: 10px;
    transition: transform 0.3s;
}
.footer-social a:hover {
    transform: translateY(-3px);
}
.footer-social img {
    width: 32px;
    height: 32px;
}
.footer-bottom {
    text-align: center;
    padding-top: 20px;
    margin-top: 40px;
    border-top: 1px solid #ddd;
    color: #888;
    font-size: 14px;
}
.support-block {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.support-block p {
    margin: 0;
    color: #666;
}
.support-block strong {
    font-size: 18px;
    color: #d9534f;
    display: block;
    margin-top: 5px;
}
</style>

<footer class="site-footer">
    <div class="row">
        <div class="col-md-4 col-sm-12 footer-contact">
            <h4>SHOP QUẦN ÁO MINI</h4>
            <address>
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 120 Phường Yên Lãng, Thịnh Quang, Đống Đa, Hà Nội<br>
                <span class="glyphicon glyphicon-phone" aria-hidden="true"></span> 0123456789<br>
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> contact@shopmini.vn
            </address>
            <div class="footer-social">
                <a href="#"><img src="<?php echo base_url(); ?>upload/icon/facebook.png" alt="Facebook"></a>
                <a href="#"><img src="<?php echo base_url(); ?>upload/icon/twitter.png" alt="Twitter"></a>
                <a href="#"><img src="<?php echo base_url(); ?>upload/icon/google.png" alt="Google"></a>
            </div>
        </div>
        
        <div class="col-md-2 col-sm-4 col-xs-6">
            <h4>Về chúng tôi</h4>
            <ul>
                <li><a href="#">Giới thiệu về shop</a></li>
                <li><a href="#">Các mức vi phạm</a></li>
                <li><a href="#">Quy chế hoạt động</a></li>
            </ul>
        </div>
        
        <div class="col-md-3 col-sm-4 col-xs-6">
            <h4>Hỗ trợ khách hàng</h4>
            <ul>
                <li><a href="#">Bảo vệ người mua</a></li>
                <li><a href="#">Quy định đối với người mua</a></li>
                <li><a href="#">Câu hỏi thường gặp</a></li>
                <li><a href="#">Hướng dẫn mua hàng</a></li>
            </ul>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-12">
            <h4>Tổng đài hỗ trợ</h4>
            <div class="support-block">
                <p><small>Tư vấn miễn phí (24/7)</small></p>
                <strong>1800 3333</strong>
            </div>
            <div class="support-block">
                <p><small>Góp ý, phản ánh (8h - 22h)</small></p>
                <strong>1800 3334</strong>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> SHOP quần áo mini. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
#custom-toast-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99999;
    align-items: center;
    justify-content: center;
}
#custom-toast-box {
    background: #363636;
    color: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    animation: toastPop 0.3s ease-out;
}
@keyframes toastPop {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
.toast-icon {
    font-size: 60px;
    color: #00d26a;
    margin-bottom: 10px;
    display: inline-block;
}
.toast-btn {
    background: #ffffff;
    color: #333333;
    border: none;
    padding: 10px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 500;
    margin-top: 20px;
    display: inline-block;
    text-decoration: none;
    transition: background 0.2s;
}
.toast-btn:hover {
    background: #eeeeee;
    text-decoration: none;
    color: #333;
}
</style>

<div id="custom-toast-overlay">
    <div id="custom-toast-box">
        <div class="toast-icon">
            <span class="glyphicon glyphicon-ok-sign"></span>
        </div>
        <h3 style="font-size: 18px; font-weight: normal; margin: 10px 0 0 0;" id="toast-msg">Sản phẩm đã được thêm vào giỏ hàng</h3>
        <a href="<?php echo base_url('cart'); ?>" class="toast-btn">Xem giỏ hàng</a>
    </div>
</div>

<script>
$(document).ready(function() {
    $('a[href*="cart/add/"]').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Hiển thị thông báo đẹp
                    $('#toast-msg').text(response.message);
                    $('#custom-toast-overlay').css('display', 'flex').hide().fadeIn();
                    
                    $('.badge').text(response.cart_total);
                    // Lấy giao diện giỏ hàng mới
                    $.get(location.href, function(data) {
                        var newDropdown = $(data).find('.glyphicon-shopping-cart').closest('.dropdown').html();
                        if (newDropdown) {
                            $('.glyphicon-shopping-cart').closest('.dropdown').html(newDropdown);
                        }
                    });
                    
                    // Tự động ẩn sau 3 giây
                    setTimeout(function() {
                        $('#custom-toast-overlay').fadeOut();
                    }, 3000);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert("Có lỗi xảy ra, vui lòng thử lại.");
            }
        });
    });

    // Bấm ra ngoài để đóng popup
    $('#custom-toast-overlay').click(function(e) {
        if (e.target === this) {
            $(this).fadeOut();
        }
    });
});
</script>
