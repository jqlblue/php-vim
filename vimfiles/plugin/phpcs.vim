function! Phpcs()
  " phpcs 命令的路径和参数, 请根据环境自行修改
  ! /usr/bin/phpcs --standard=Zend "%"
  cwindow
endfunction
" :w 自动验证语法
" autocmd BufWritePost *.php call Phpcs()
" :Phpcs 验证语法
command! Phpcs execute Phpcs()
