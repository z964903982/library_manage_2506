/*
 Navicat Premium Dump SQL

 Source Server         : 20240602
 Source Server Type    : MySQL
 Source Server Version : 80400 (8.4.0)
 Source Host           : localhost:3306
 Source Schema         : library_manage_2506

 Target Server Type    : MySQL
 Target Server Version : 80400 (8.4.0)
 File Encoding         : 65001

 Date: 23/06/2025 10:44:12
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `admin_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contact` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `position` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `permission` int NULL DEFAULT 1,
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`admin_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('001', '小林', '13293672934', '图书管理员', 1, '001');
INSERT INTO `admin` VALUES ('002', '吕一', '13293672823', '图书管理员', 2, '002');

-- ----------------------------
-- Table structure for book
-- ----------------------------
DROP TABLE IF EXISTS `book`;
CREATE TABLE `book`  (
  `ISBN` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `publisher` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `publish_date` year NULL DEFAULT NULL,
  `price` decimal(10, 2) NULL DEFAULT NULL,
  `category` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total_copies` int NULL DEFAULT 0,
  `available_copies` int NULL DEFAULT 0,
  `status` enum('在架','借出','遗失','下架') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '在架',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ISBN`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of book
-- ----------------------------
INSERT INTO `book` VALUES ('978-7-03-062965-4', '信息中心网络关键理论与技术', '张明川', '科学出版社', 2019, 95.00, '计算机', '自科（二楼北）', 7, 7, '在架', '本书在归纳分析国内外信息中心网络相关科研成果的基础上，研究信息中心网络的名字査找、缓存、路由及拥塞控制等问题，主要内容包括：名字快速查找方法，提出基于名字拆分的查找策略；网络内容缓存策略，提出基于请求内容关联性的预缓存策略、路由器缓存准入策略、基于节点利用比的缓存策略、基于内容分块流行度和收益的缓存策略；自适应路由转发策略，提出基于增强学习的自适应路由转发策略、支持资源适配的可重构路由策略；自适应拥塞控制策略，提出基于深度学习的拥塞控制算法。', '../../images/1.jpg');
INSERT INTO `book` VALUES ('978-7-108-03307-9', '围城', '钱钟书', '生活读书新知三联书店', 2009, 29.00, '文学', '文学阅览室', 5, 5, '在架', '钱钟书（1910～1998），是当代中国著名的学者、作家。他的著述，如广为传播的《谈艺录》、《管锥编》、《围城》等。', '../../images/8.jpg');
INSERT INTO `book` VALUES ('978-7-115-23890-0', '线性代数及其应用导论', '阿波斯托尔', '人民邮电出版社', 2010, 59.00, '数学', '自科（二楼南）', 6, 6, '在架', '本书简体字中文版由John Wiley & Sons, Inc.授权出版。', '../../images/10.jpg');
INSERT INTO `book` VALUES ('978-7-213-04692-6', '明朝那些事儿', '当年明月', '浙江人民出版社', 2011, 368.00, '社科', '社科（三楼南）', 3, 3, '在架', '第一部, 朱元璋:从和尚到皇帝 ;第二部, 朱棣:逆子还是明君 ;第三部, 太监弄乱的王朝,第四部, 妖孽横行的宫廷 ;第五部, 内阁不相信眼泪;第六部, 帝国·风雨欲来;第七部, 拐弯中的帝国;第八部, 人间再无魏忠贤;第九部, 1644, 最后的较量.', '../../images/11.jpg');
INSERT INTO `book` VALUES ('978-7-302-21443-4', '自动控制原理习题详解', '王建辉', '清华大学出版社', 2010, 25.00, '技术', '\r\n自科（二楼北）', 1, 0, '借出', '普通高等教育“十一五”国家级规划教材 国家精品课程教材', '../../images/2.jpg');
INSERT INTO `book` VALUES ('978-7-302-33098-1', '软件工程导论', '张海藩, 牟永敏', '清华大学出版社', 2013, 39.50, '计算机', '自科（二楼北）', 8, 8, '在架', '“十二五”普通高等教育本科国家级规划教材 北京高等教育精品教材', '../../images/9.jpg');
INSERT INTO `book` VALUES ('978-7-5067-7413-0', '生物工程', '王旻', '中国医药科技出版社', 2015, 49.00, '医学', '自科（二楼南）', 5, 5, '在架', '本书供生物制药、生物技术、生物工程和海洋药学专业用。\r\n', '../../images/12.jpg');
INSERT INTO `book` VALUES ('978-7-5180-8734-1', '用于密封液体的磁流体旋转密封', '王虎军', '中国纺织出版社有限公司', 2021, 88.00, '技术', '自科（二楼北）', 3, 3, '在架', '本书以磁流体为研究对象，主要内容包括概述磁流体的特性、制备方法、典型应用及磁流体密封的研究现状；阐述了用于密封液体的直接接触型和气体隔离型两种磁流体旋转密封的理论基础；设计了两种相应的磁流体密封结构，搭建了密封实验台；通过仿真和实验对两种结构的密封性能进行了对比研究，结果表明书中设计的密封结构有效提升了磁流体密封液体的性能。\r\n', '../../images/7.jpg');
INSERT INTO `book` VALUES ('978-7-5443-6232-0', '余罪', '常书欣', '海南出版社', 2014, 32.00, '文学', '文学阅览室', 6, 6, '在架', '本书为您揭开的是一张令人触目惊心的当下社会犯罪网络。从混迹人群中的扒手，到躲在深山老林里的悍匪，从横行街头的流氓，到逡巡在海岸线边缘的毒枭；他们似乎离我们很远，似乎又很近，看似悄无声息，却又如影随形；作者所描写的，正是这个光怪陆离而又真实存在的地下世界。', '../../images/3.jpg');
INSERT INTO `book` VALUES ('978-7-5672-1965-6', '数学分析习题课教程', '卞秋香', '苏州大学出版社', 2017, 42.00, '数学', '自科（二楼南）', 10, 10, '在架', '本书主要内容包括：实数集与函数、数列极限、函数极限、函数的连续性、导数与微分、微分中值定理及其应用、实数的完备性、不定积分、定积分等。\r\n', '../../images/6.jpg');
INSERT INTO `book` VALUES ('978-7-5714-4029-9', '药物的毒营养来解', '(美) 苏西·科恩', '北京科学技术出版社', 2024, 89.00, '医学', '新书阅览室', 5, 5, '在架', '不愉快、不舒服和无法解释的副作用？本书是你的副作用解决方案。处方药和非处方药每年帮助数百万患有毁灭性疾病和慢性病的人。但在这个过程中，这些药物也会耗尽人体自然储存的维生素、矿物质和荷尔蒙，这些正是你保持高能量水平、抵御感染和保持健康所需的营养素。药剂师苏西·科恩（SuzyCohen）将这些药物称为“药物盗匪”，她说，必须补充药物盗匪从你身上偷来的东西，以便感觉最佳，避免副作用。', '../../images/4.jpg');
INSERT INTO `book` VALUES ('978-7-5722-7283-7', '突发性公共事件中的心理管理', '刘永芳', '浙江教育出版社', 2024, 65.00, '心理', '新书阅览室', 9, 6, '遗失', '本书是在作者长期以来聚焦当代管理以人为本的价值思潮和未来学科发展的需求，大胆创新，系统构建，提出并系统阐述的“心理管理学”思想基础上，把相关观点、理念及成果向社会治理和公共管理领域的延伸和推广，从积极心理学及建设性的角度，分不同专题论及突发性公共事件中主要的社会心理现象及其特点、成因、后果及对策，以期对突发性公共事件乃至非此类事件中人们的心理管理问题起到抛砖引玉之效。', '../../images/5.jpg');

-- ----------------------------
-- Table structure for borrowrecord
-- ----------------------------
DROP TABLE IF EXISTS `borrowrecord`;
CREATE TABLE `borrowrecord`  (
  `borrow_id` int NOT NULL AUTO_INCREMENT,
  `ISBN` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date NULL DEFAULT NULL,
  `renew_count` int NULL DEFAULT 0,
  `status` enum('借出','已还','逾期') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '借出',
  PRIMARY KEY (`borrow_id`) USING BTREE,
  INDEX `ISBN`(`ISBN` ASC) USING BTREE,
  INDEX `student_id`(`student_id` ASC) USING BTREE,
  CONSTRAINT `borrowrecord_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `borrowrecord_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of borrowrecord
-- ----------------------------
INSERT INTO `borrowrecord` VALUES (1, '978-7-03-062965-4', '202510002', '2025-06-17', '2025-08-07', '2025-06-18', 0, '已还');
INSERT INTO `borrowrecord` VALUES (2, '978-7-03-062965-4', '202510004', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (3, '978-7-5722-7283-7', '202510004', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (4, '978-7-03-062965-4', '202510004', '2025-06-17', '2025-07-31', '2025-06-18', 2, '已还');
INSERT INTO `borrowrecord` VALUES (5, '978-7-302-21443-4', '202510004', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (6, '978-7-302-21443-4', '202510004', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (7, '978-7-302-21443-4', '202510004', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (8, '978-7-302-21443-4', '202310003', '2025-02-07', '2025-03-07', '2025-02-14', 0, '已还');
INSERT INTO `borrowrecord` VALUES (9, '978-7-5443-6232-0', '202510004', '2025-02-17', '2025-03-17', '2025-06-18', 0, '已还');
INSERT INTO `borrowrecord` VALUES (10, '978-7-5722-7283-7', '202510005', '2025-02-17', '2025-03-17', '2025-06-17', 1, '已还');
INSERT INTO `borrowrecord` VALUES (11, '978-7-302-21443-4', '202510005', '2025-03-17', '2025-04-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (12, '978-7-5443-6232-0', '202510005', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (13, '978-7-5443-6232-0', '202510005', '2025-03-17', '2025-04-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (14, '978-7-302-21443-4', '202510005', '2025-06-17', '2025-07-17', '2025-06-17', 0, '已还');
INSERT INTO `borrowrecord` VALUES (15, '978-7-302-21443-4', '202510005', '2025-06-17', '2025-07-17', '2025-06-18', 0, '已还');
INSERT INTO `borrowrecord` VALUES (16, '978-7-5722-7283-7', '202510003', '2025-06-17', '2025-07-17', '2025-06-18', 0, '已还');
INSERT INTO `borrowrecord` VALUES (17, '978-7-03-062965-4', '202510003', '2025-06-17', '2025-07-17', '2025-06-18', 0, '已还');
INSERT INTO `borrowrecord` VALUES (18, '978-7-03-062965-4', '202510004', '2025-03-18', '2025-04-18', '2025-06-18', 0, '已还');
INSERT INTO `borrowrecord` VALUES (19, '978-7-03-062965-4', '3221902003', '2025-06-18', '2025-07-18', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (20, '978-7-302-33098-1', '3221902003', '2025-06-18', '2025-07-18', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (21, '978-7-03-062965-4', '202510004', '2025-06-20', '2025-07-20', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (22, '978-7-302-33098-1', '202510007', '2025-06-20', '2025-07-20', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (23, '978-7-115-23890-0', '202510007', '2025-06-20', '2025-07-20', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (24, '978-7-213-04692-6', '202510007', '2025-06-20', '2025-07-15', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (25, '978-7-03-062965-4', '202510001', '2025-06-20', '2025-07-15', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (26, '978-7-302-21443-4', '202510002', '2025-06-20', '2025-07-15', NULL, 0, '借出');
INSERT INTO `borrowrecord` VALUES (27, '978-7-302-33098-1', '202510003', '2025-06-20', '2025-07-15', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (28, '978-7-302-21443-4', '202510003', '2025-06-20', '2025-07-15', '2025-06-20', 0, '已还');
INSERT INTO `borrowrecord` VALUES (29, '978-7-302-33098-1', '202510003', '2025-04-20', '2025-05-15', '2025-06-20', 0, '已还');

-- ----------------------------
-- Table structure for fine
-- ----------------------------
DROP TABLE IF EXISTS `fine`;
CREATE TABLE `fine`  (
  `fine_id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `borrow_id` int NULL DEFAULT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `reason` enum('逾期','遗失','损坏') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('未缴','已缴') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '未缴',
  `admin_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`fine_id`) USING BTREE,
  INDEX `student_id`(`student_id` ASC) USING BTREE,
  INDEX `borrow_id`(`borrow_id` ASC) USING BTREE,
  INDEX `admin_id`(`admin_id` ASC) USING BTREE,
  CONSTRAINT `fine_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fine_ibfk_2` FOREIGN KEY (`borrow_id`) REFERENCES `borrowrecord` (`borrow_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fine_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fine
-- ----------------------------
INSERT INTO `fine` VALUES (1, '202510004', 9, 46.00, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (2, '202510004', 9, 46.00, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (7, '202510005', 10, 46.00, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (8, '202510005', 11, 30.50, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (9, '202510005', 13, 30.50, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (10, '202510004', 9, 46.50, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (11, '202510004', 18, 30.50, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (12, '202510004', 18, 30.50, '逾期', '已缴', NULL);
INSERT INTO `fine` VALUES (13, '202510003', 29, 18.00, '逾期', '已缴', NULL);

-- ----------------------------
-- Table structure for finerule
-- ----------------------------
DROP TABLE IF EXISTS `finerule`;
CREATE TABLE `finerule`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `overdue_rate` decimal(5, 2) NOT NULL DEFAULT 0.50 COMMENT '逾期每天罚款金额',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of finerule
-- ----------------------------
INSERT INTO `finerule` VALUES (1, 0.50, '2025-06-18 16:28:50');
INSERT INTO `finerule` VALUES (2, 1.00, '2025-06-18 16:33:11');
INSERT INTO `finerule` VALUES (3, 1.00, '2025-06-18 16:33:35');
INSERT INTO `finerule` VALUES (4, 0.50, '2025-06-18 16:33:42');

-- ----------------------------
-- Table structure for reservation
-- ----------------------------
DROP TABLE IF EXISTS `reservation`;
CREATE TABLE `reservation`  (
  `reserve_id` int NOT NULL AUTO_INCREMENT,
  `ISBN` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `reserve_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('等待','已通知','取消') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '等待',
  PRIMARY KEY (`reserve_id`) USING BTREE,
  INDEX `ISBN`(`ISBN` ASC) USING BTREE,
  INDEX `student_id`(`student_id` ASC) USING BTREE,
  CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of reservation
-- ----------------------------
INSERT INTO `reservation` VALUES (1, '978-7-302-21443-4', '202510004', '2025-06-17 18:51:35', '取消');
INSERT INTO `reservation` VALUES (2, '978-7-302-21443-4', '202510005', '2025-06-17 19:08:17', '已通知');
INSERT INTO `reservation` VALUES (3, '978-7-302-21443-4', '202510003', '2025-06-17 20:23:23', '取消');
INSERT INTO `reservation` VALUES (4, '978-7-302-21443-4', '202510003', '2025-06-17 20:32:02', '已通知');
INSERT INTO `reservation` VALUES (5, '978-7-302-21443-4', '202510003', '2025-06-17 22:15:49', '已通知');
INSERT INTO `reservation` VALUES (6, '978-7-302-21443-4', '202510001', '2025-06-20 11:12:14', '已通知');
INSERT INTO `reservation` VALUES (7, '978-7-302-21443-4', '202510001', '2025-06-20 11:21:59', '等待');

-- ----------------------------
-- Table structure for review
-- ----------------------------
DROP TABLE IF EXISTS `review`;
CREATE TABLE `review`  (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `ISBN` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `rating` int NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `review_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`) USING BTREE,
  INDEX `ISBN`(`ISBN` ASC) USING BTREE,
  INDEX `student_id`(`student_id` ASC) USING BTREE,
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `review_chk_1` CHECK (`rating` between 1 and 5)
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of review
-- ----------------------------
INSERT INTO `review` VALUES (2, '978-7-302-21443-4', '202510003', 5, '好。', '2025-06-17 22:32:21');
INSERT INTO `review` VALUES (4, '978-7-03-062965-4', '202510001', 5, '很好的书。', '2025-06-18 17:11:42');
INSERT INTO `review` VALUES (5, '978-7-302-33098-1', '3221902003', 5, '好', '2025-06-18 19:36:09');

-- ----------------------------
-- Table structure for student
-- ----------------------------
DROP TABLE IF EXISTS `student`;
CREATE TABLE `student`  (
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `gender` enum('男','女') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `department` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `major` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `grade` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `class` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `contact` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('正常','冻结','挂失') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '正常',
  `max_borrow` int NULL DEFAULT 5,
  `current_borrow` int NULL DEFAULT 0,
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`student_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of student
-- ----------------------------
INSERT INTO `student` VALUES ('202310003', '赵甲', '男', '文学院', '英语', '2023', '1班', '13248455326', 'zhaojia@ujs.edu.cn', '正常', 5, 0, '202310003');
INSERT INTO `student` VALUES ('202510001', '李华', '女', '计算机学院', '软件工程', '2025', '2班', '13293661851', 'lihua@ujs.edu.cn', '正常', 5, 0, '202510001');
INSERT INTO `student` VALUES ('202510002', '王明', '男', '外国语学院', '会计学', '2025', '3班', '13933825939', 'wangming@ujs.edu.cn', '正常', 5, 1, '202510002');
INSERT INTO `student` VALUES ('202510003', '彭三', '女', '物理学院', '物理学', '2025', '1班', '13982928922', 'pengsan@ujs.edu.cn', '正常', 5, 0, '202510003');
INSERT INTO `student` VALUES ('202510004', '刘一', '女', '计算机学院', '软件工程', '2025', '2班', '13293667849', 'liuyi@ujs.edu.cn', '正常', 5, 0, '202510004');
INSERT INTO `student` VALUES ('202510005', '沈二', '女', '计算机学院', '通信工程', '2025', '3班', '13293689298', 'shener@ujs.edu.cn', '正常', 5, 0, '202510005');
INSERT INTO `student` VALUES ('202510006', '郑六', '女', '计算机学院', '计算机科学与技术', '2025', '3班', '1390743839', 'zhenliu@ujs.edu.cn', '正常', 5, 0, '202510006');
INSERT INTO `student` VALUES ('202510007', '王明', '女', '医学院', '临床医学', '2025', '1班', '13267327328', 'wangming@ujs.edu.cn', '正常', 5, 0, '202510007');
INSERT INTO `student` VALUES ('3221902003', '张诗凝', '女', '计算机学院', '软件工程', '2022', '2班', '13628989822', 'zhangshining@ujs.edu.cn', '正常', 5, 0, '3221902003');

-- ----------------------------
-- Table structure for systemconfig
-- ----------------------------
DROP TABLE IF EXISTS `systemconfig`;
CREATE TABLE `systemconfig`  (
  `config_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `config_value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of systemconfig
-- ----------------------------
INSERT INTO `systemconfig` VALUES ('default_borrow_days', '25', '2025-06-20 09:26:43');
INSERT INTO `systemconfig` VALUES ('max_borrow_limit', '5', '2025-06-20 08:37:24');

-- ----------------------------
-- View structure for borrow_by_category
-- ----------------------------
DROP VIEW IF EXISTS `borrow_by_category`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `borrow_by_category` AS select `b`.`category` AS `category`,count(0) AS `borrow_count` from (`borrowrecord` `br` join `book` `b` on((`br`.`ISBN` = `b`.`ISBN`))) group by `b`.`category` order by `borrow_count` desc;

-- ----------------------------
-- View structure for borrow_by_month
-- ----------------------------
DROP VIEW IF EXISTS `borrow_by_month`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `borrow_by_month` AS select date_format(`borrowrecord`.`borrow_date`,'%Y-%m') AS `month`,count(0) AS `borrow_count` from `borrowrecord` group by date_format(`borrowrecord`.`borrow_date`,'%Y-%m') order by `month` desc;

-- ----------------------------
-- View structure for departmentborrowstats
-- ----------------------------
DROP VIEW IF EXISTS `departmentborrowstats`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `departmentborrowstats` AS select `s`.`department` AS `department`,count(0) AS `total_borrow`,sum((case when (`br`.`status` = '逾期') then 1 else 0 end)) AS `overdue_count` from (`borrowrecord` `br` join `student` `s` on((`br`.`student_id` = `s`.`student_id`))) group by `s`.`department`;

-- ----------------------------
-- View structure for overdue_analysis
-- ----------------------------
DROP VIEW IF EXISTS `overdue_analysis`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `overdue_analysis` AS select `s`.`department` AS `department`,count(`f`.`fine_id`) AS `overdue_count`,sum(`f`.`amount`) AS `total_overdue_amount` from (`fine` `f` join `student` `s` on((`f`.`student_id` = `s`.`student_id`))) where (`f`.`reason` = '逾期') group by `s`.`department`;

-- ----------------------------
-- View structure for overdue_by_student
-- ----------------------------
DROP VIEW IF EXISTS `overdue_by_student`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `overdue_by_student` AS select `s`.`student_id` AS `student_id`,`s`.`name` AS `name`,`s`.`department` AS `department`,count(`f`.`fine_id`) AS `overdue_count`,sum(`f`.`amount`) AS `total_overdue_amount` from (`fine` `f` join `student` `s` on((`f`.`student_id` = `s`.`student_id`))) where (`f`.`reason` = '逾期') group by `s`.`student_id`,`s`.`name`,`s`.`department`;

-- ----------------------------
-- View structure for popularbooks
-- ----------------------------
DROP VIEW IF EXISTS `popularbooks`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `popularbooks` AS select `b`.`ISBN` AS `ISBN`,`b`.`title` AS `title`,count(0) AS `borrow_count` from (`borrowrecord` `br` join `book` `b` on((`br`.`ISBN` = `b`.`ISBN`))) group by `b`.`ISBN` order by `borrow_count` desc limit 20;

-- ----------------------------
-- Procedure structure for AutoMarkOverdue
-- ----------------------------
DROP PROCEDURE IF EXISTS `AutoMarkOverdue`;
delimiter ;;
CREATE PROCEDURE `AutoMarkOverdue`()
BEGIN
    UPDATE BorrowRecord
    SET status = '逾期'
    WHERE return_date IS NULL
      AND due_date < CURDATE()
      AND status != '逾期';
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for GenerateDailyReport
-- ----------------------------
DROP PROCEDURE IF EXISTS `GenerateDailyReport`;
delimiter ;;
CREATE PROCEDURE `GenerateDailyReport`()
BEGIN
    DECLARE report_day DATE;
    SET report_day = CURDATE();

    -- 删除今日已有报告（避免重复）
    DELETE FROM MonthlyReport WHERE report_date = report_day;

    INSERT INTO MonthlyReport (report_date, department, borrow_count)
    SELECT
        report_day,
        s.department,
        COUNT(*) AS borrow_count
    FROM BorrowRecord br
    JOIN Student s ON br.student_id = s.student_id
    WHERE DATE(br.borrow_date) = report_day
    GROUP BY s.department;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for GenerateMonthlyReport
-- ----------------------------
DROP PROCEDURE IF EXISTS `GenerateMonthlyReport`;
delimiter ;;
CREATE PROCEDURE `GenerateMonthlyReport`(IN month INT, IN year INT)
BEGIN
  SELECT department, COUNT(*) AS borrow_count
  FROM BorrowRecord br
  JOIN Student s ON br.student_id = s.student_id
  WHERE MONTH(br.borrow_date) = month AND YEAR(br.borrow_date) = year
  GROUP BY department;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for InitStudentAccounts
-- ----------------------------
DROP PROCEDURE IF EXISTS `InitStudentAccounts`;
delimiter ;;
CREATE PROCEDURE `InitStudentAccounts`()
BEGIN
  UPDATE Student 
  SET current_borrow = 0, 
      status = '正常'
  WHERE status != '挂失';  -- 保留挂失状态
END
;;
delimiter ;

-- ----------------------------
-- Event structure for e_generate_report
-- ----------------------------
DROP EVENT IF EXISTS `e_generate_report`;
delimiter ;;
CREATE EVENT `e_generate_report`
ON SCHEDULE
EVERY '1' DAY STARTS '2025-06-19 00:00:00'
DO CALL GenerateDailyReport()
;;
delimiter ;

-- ----------------------------
-- Event structure for e_mark_overdue
-- ----------------------------
DROP EVENT IF EXISTS `e_mark_overdue`;
delimiter ;;
CREATE EVENT `e_mark_overdue`
ON SCHEDULE
EVERY '1' DAY STARTS '2025-06-19 00:00:00'
DO CALL AutoMarkOverdue()
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table borrowrecord
-- ----------------------------
DROP TRIGGER IF EXISTS `after_borrow`;
delimiter ;;
CREATE TRIGGER `after_borrow` AFTER INSERT ON `borrowrecord` FOR EACH ROW BEGIN
  -- 减少图书可借数量
  UPDATE Book 
  SET available_copies = available_copies - 1 
  WHERE ISBN = NEW.ISBN;

  -- 增加学生已借数量
  UPDATE Student 
  SET current_borrow = current_borrow + 1 
  WHERE student_id = NEW.student_id;

  -- 如果可借数为0，将图书状态改为“借出”
  UPDATE Book 
  SET status = '借出' 
  WHERE ISBN = NEW.ISBN AND available_copies = 0;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table borrowrecord
-- ----------------------------
DROP TRIGGER IF EXISTS `after_return`;
delimiter ;;
CREATE TRIGGER `after_return` AFTER UPDATE ON `borrowrecord` FOR EACH ROW BEGIN
  IF NEW.return_date IS NOT NULL THEN
    -- 增加图书可借副本
    UPDATE Book 
    SET available_copies = available_copies + 1 
    WHERE ISBN = NEW.ISBN;

    -- 减少学生当前借阅数
    UPDATE Student 
    SET current_borrow = current_borrow - 1 
    WHERE student_id = NEW.student_id;

    -- 判断是否逾期，若逾期则插入罚款记录
    IF NEW.return_date > NEW.due_date THEN
      INSERT INTO Fine (student_id, borrow_id, amount, reason)
      SELECT
        NEW.student_id,
        NEW.borrow_id,
        DATEDIFF(NEW.return_date, NEW.due_date) * overdue_rate,
        '逾期'
      FROM FineRule
      ORDER BY id DESC LIMIT 1;
    END IF;

    -- 恢复图书状态
    UPDATE Book 
    SET status = '在架' 
    WHERE ISBN = NEW.ISBN AND status = '借出';

    -- 通知预约人
    UPDATE reservation
    SET status = '已通知'
    WHERE reserve_id = (
        SELECT reserve_id FROM (
            SELECT reserve_id 
            FROM reservation 
            WHERE ISBN = NEW.ISBN AND status = '等待' 
            ORDER BY reserve_time ASC 
            LIMIT 1
        ) AS earliest
    );
  END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table borrowrecord
-- ----------------------------
DROP TRIGGER IF EXISTS `after_mark_overdue`;
delimiter ;;
CREATE TRIGGER `after_mark_overdue` AFTER UPDATE ON `borrowrecord` FOR EACH ROW BEGIN
  DECLARE days_late INT;

  -- 判断是否是人工标记为“逾期”
  IF OLD.status <> '逾期' AND NEW.status = '逾期' THEN

    -- 只有未归还的记录才需要处理罚款
    IF NEW.return_date IS NULL THEN

      -- 计算逾期天数
      SET days_late = DATEDIFF(CURDATE(), NEW.due_date);

      -- 若真正逾期
      IF days_late > 0 THEN

        -- 检查是否已存在罚款记录，防止重复
        IF NOT EXISTS (
          SELECT 1 FROM Fine WHERE borrow_id = NEW.borrow_id AND reason = '逾期'
        ) THEN
          INSERT INTO Fine (student_id, borrow_id, amount, reason)
           SELECT
        NEW.student_id,
        NEW.borrow_id,
        DATEDIFF(NEW.return_date, NEW.due_date) * overdue_rate,
        '逾期'
      FROM FineRule
      ORDER BY id DESC LIMIT 1;
        END IF;

      END IF;

    END IF;

  END IF;

END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table fine
-- ----------------------------
DROP TRIGGER IF EXISTS `freeze_student_on_fine`;
delimiter ;;
CREATE TRIGGER `freeze_student_on_fine` AFTER INSERT ON `fine` FOR EACH ROW BEGIN
    UPDATE Student
    SET status = '冻结'
    WHERE student_id = NEW.student_id;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table fine
-- ----------------------------
DROP TRIGGER IF EXISTS `unfreeze_student_on_payment`;
delimiter ;;
CREATE TRIGGER `unfreeze_student_on_payment` AFTER UPDATE ON `fine` FOR EACH ROW BEGIN
    IF OLD.status = '未缴' AND NEW.status = '已缴' THEN
        -- 判断该学生是否仍有未缴罚款
        IF NOT EXISTS (
            SELECT 1 FROM Fine 
            WHERE student_id = NEW.student_id AND status = '未缴'
        ) THEN
            UPDATE Student
            SET status = '正常'
            WHERE student_id = NEW.student_id;
        END IF;
    END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
