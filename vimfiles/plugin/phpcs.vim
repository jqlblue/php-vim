function! Phpcs()
  " phpcs 命令的路径和参数, 请根据环境自行修改
    let l:filename=@%
    let l:phpcs_output=system('phpcs --report=csv --standard=Zend '.l:filename)
    let l:phpcs_list=split(l:phpcs_output, "\n")
    unlet l:phpcs_list[0]
    cexpr l:phpcs_list
    cwindow
endfunction

" :w 自动验证语法
autocmd BufWritePost *.php call Phpcs()
" :Phpcs 验证语法
set errorformat=\"%f\"\\,%l\\,%c\\,%t%*[a-zA-Z]\\,\"%m\"\\,%*[a-zA-Z0-9_.-]\\,%*[0-9]
command! Phpcs execute Phpcs()
