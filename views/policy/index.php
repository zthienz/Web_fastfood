<div class="policy-container">
    <div class="policy-header">
        <h1><?= e($policy['title']) ?></h1>
        <p class="policy-intro"><?= e($policy['content']['intro']) ?></p>
    </div>
    
    <div class="policy-content">
        <?php foreach ($policy['content']['sections'] as $section): ?>
            <div class="policy-section">
                <h2><?= e($section['title']) ?></h2>
                <ul class="policy-list">
                    <?php foreach ($section['content'] as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="policy-contact">
        <div class="contact-box">
            <h3>Cần hỗ trợ thêm?</h3>
            <p>Liên hệ với chúng tôi để được tư vấn chi tiết:</p>
            <div class="contact-info">
                <p><strong>📞 Hotline:</strong> 1900-xxxx</p>
                <p><strong>✉️ Email:</strong> support@fastfood.com</p>
                <p><strong>🕒 Thời gian:</strong> 6:00 - 23:00 (Tất cả các ngày)</p>
            </div>
            <a href="index.php?page=contact" class="contact-btn">Liên hệ ngay</a>
        </div>
    </div>
</div>