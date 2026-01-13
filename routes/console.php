<?php

use Illuminate\Support\Facades\Schedule;

// Tự động cập nhật trạng thái chứng chỉ hết hạn mỗi ngày lúc 00:00
Schedule::command('certificates:update-status')->daily();

