-- SkillSwap 初始化資料

-- 插入示範用戶（密碼都是 password123）
INSERT INTO `User` (`name`, `email`, `password_hash`) VALUES 
('Alice Chen', 'alice@example.com', '$2y$10$LQMoEf7uNpiDilsuUhZpDuKP08m8sbecerWbLdBFH.jtWwOCa8xHS'),
('Bob Wang', 'bob@example.com', '$2y$10$LQMoEf7uNpiDilsuUhZpDuKP08m8sbecerWbLdBFH.jtWwOCa8xHS'),
('Carol Li', 'carol@example.com', '$2y$10$LQMoEf7uNpiDilsuUhZpDuKP08m8sbecerWbLdBFH.jtWwOCa8xHS');

-- 插入示範技能貼文
INSERT INTO `SkillPost` (`user_id`, `title`, `description`, `point_cost`) VALUES 
(1, 'Python 程式教學', '提供 Python 基礎程式設計教學，包含語法基礎、資料結構、函式設計等。適合初學者，一對一教學。', 50),
(1, '英文會話練習', '母語等級英文能力，提供日常會話練習、商務英文指導，幫助提升口語表達能力。', 30),
(2, '吉他彈唱教學', '10年吉他演奏經驗，教授民謠吉他、指彈技巧、和弦進行，可搭配唱歌技巧指導。', 40),
(2, '攝影技術指導', '專業攝影師，提供相機操作、構圖技巧、後製修圖等攝影相關技能教學。', 60),
(3, '料理烹飪教學', '家庭料理、中式炒菜、西式烘焙等料理技巧教學，從食材選購到完成料理的完整指導。', 35),
(3, '瑜珈課程指導', '具備瑜珈教練證照，提供哈達瑜珈、流動瑜珈課程，適合各程度學員。', 45);

-- 插入示範點數交易（給每個用戶初始點數）
INSERT INTO `PointTransaction` (`from_user_id`, `to_user_id`, `amount`, `reason`) VALUES 
(1, 1, 100, '新手贈送'),
(2, 2, 100, '新手贈送'),
(3, 3, 100, '新手贈送');

-- 插入示範預約
INSERT INTO `ExchangeBooking` (`post_id`, `requester_id`, `status`, `scheduled_at`) VALUES 
(1, 2, '待確認', '2025-06-01 14:00:00'),
(3, 1, '已完成', '2025-05-28 10:00:00'),
(5, 2, '待確認', '2025-06-02 16:00:00');

-- 插入示範留言
INSERT INTO `Comment` (`post_id`, `user_id`, `content`) VALUES 
(1, 2, '請問是線上教學還是面對面呢？'),
(1, 1, '可以配合您的需求，線上或面對面都可以安排。'),
(3, 3, '我是完全沒有基礎的初學者，也可以教嗎？'),
(3, 2, '當然可以！我會從最基礎的和弦開始教起。'),
(5, 1, '想學做甜點，請問有教烘焙嗎？'),
(5, 3, '有的！西式甜點、蛋糕都可以教。');
