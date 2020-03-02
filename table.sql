-- ----------------------------
-- Table structure for b_latest_block
-- ----------------------------
DROP TABLE IF EXISTS `b_latest_block`;
CREATE TABLE `b_latest_block`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'symbol btc,eth',
  `hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'hashID',
  `block_index` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'height index value',
  `height` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'height value',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'add time',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'blockchain_height_table' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of b_latest_block
-- ----------------------------
INSERT INTO `b_latest_block` VALUES (1, 'btc', '00000000000000000008ecdeb6e784871183c8cdd4b8e0fe24619399d9f6b2f0', 0, 619807, 1583135900);

-- ----------------------------
-- Table structure for b_local_block
-- ----------------------------
DROP TABLE IF EXISTS `b_local_block`;
CREATE TABLE `b_local_block`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'symbol btc,eth',
  `hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'hashID',
  `block_index` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'blockchain index value',
  `height` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'blockchain height value',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'add time',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'local_height_table' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of b_local_block
-- ----------------------------
INSERT INTO `b_local_block` VALUES (1, 'btc', '00000000000000000011f0a82573451b7877cdca995a4890d3cc8d180d653931', 0, 619805, 1583135900);
INSERT INTO `b_local_block` VALUES (4, 'btc', '0000000000000000000530fca1b42f798cb443f4b091d49ca2fe32b3d78e0081', 0, 619806, 1583138000);

-- ----------------------------
-- Table structure for b_member_recharge_address
-- ----------------------------
DROP TABLE IF EXISTS `b_member_recharge_address`;
CREATE TABLE `b_member_recharge_address`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `symbol` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'symbol btc; eth',
  `member_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'recharge_address',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT 'add_time',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `member_address`(`member_address`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'member_recharge_address_table' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of b_member_recharge_address
-- ----------------------------
INSERT INTO `b_member_recharge_address` VALUES (1, 1, 'btc', '127VbtovVBNoametAcZwG1HJtirPW6oLjp', 1583135900);

-- ----------------------------
-- Table structure for b_recharge_record
-- ----------------------------
DROP TABLE IF EXISTS `b_recharge_record`;
CREATE TABLE `b_recharge_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `recharge_address` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `symbol` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'symbol btc; eth',
  `amount` decimal(20, 8) NOT NULL COMMENT 'recharge_amount',
  `txid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'hashID',
  `status` tinyint(4) NULL DEFAULT 0,
  `remark` varchar(125) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'remark',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT 'create_time',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `member_id`(`member_id`, `symbol`, `txid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'member_recharge_record_table' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of b_recharge_record
-- ----------------------------
INSERT INTO `b_recharge_record` VALUES (1, 1, '127VbtovVBNoametAcZwG1HJtirPW6oLjp', 'btc', 1.07130000, 'e9e547705c7bd084e23f15b6a4f088478d71b7758706843c33a0d5e8eaf09a5a', 1, NULL, 1583137999);

