--
-- Database: `cloud`
--

-- --------------------------------------------------------

--
-- Table structure for table `yc_config`
--

CREATE TABLE `yc_config` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_bin NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `yc_filecache`
--

CREATE TABLE `yc_filecache` (
  `id` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL,
  `owner_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_bin NOT NULL,
  `type` int(1) NOT NULL,
  `size` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `absolute_path` varchar(4000) COLLATE utf8_bin NOT NULL,
  `absolute_path_hash` char(32) COLLATE utf8_bin NOT NULL,
  `relative_path` varchar(4000) COLLATE utf8_bin NOT NULL,
  `relative_path_hash` char(32) COLLATE utf8_bin NOT NULL,
  `SHA1` char(40) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `yc_users`
--

CREATE TABLE `yc_users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `yc_config`
--
ALTER TABLE `yc_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `yc_filecache`
--
ALTER TABLE `yc_filecache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `relative_path_hash` (`relative_path_hash`),
  ADD UNIQUE KEY `absolute_path_hash` (`absolute_path_hash`),
  ADD KEY `absolute_path_hash_2` (`absolute_path_hash`),
  ADD KEY `relative_path_hash_2` (`relative_path_hash`),
  ADD KEY `pid` (`pid`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `yc_users`
--
ALTER TABLE `yc_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `yc_config`
--
ALTER TABLE `yc_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `yc_filecache`
--
ALTER TABLE `yc_filecache`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `yc_users`
--
ALTER TABLE `yc_users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
