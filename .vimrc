" .vimrc
" See: http://vimdoc.sourceforge.net/htmldoc/options.html for details

" Source local settings
if filereadable("~/.vimrc")
    source ~/.vimrc
endif

" disable VI's compatible mode（关闭vi兼容模式）
set nocompatible
" set encoding=utf-8（设置支持多语言）
set fileencodings=utf-8,gbk,default,latin1
"set gui options(设置字体，配色方案)
if has("gui_running")
set guifont=Courier\ New\ 14
" on windows,use "set guifont=Courier:14 "
" set color schema(设置配色方案)
" colorscheme oceandeep
endif

" Basic editing options
set number          " Show line numbers.显示行号
set expandtab       " Use the appropriate number of spaces to insert a <Tab>.用空格替换tab
set shiftwidth=2    " Number of spaces to use for each step of (auto)indent.自动缩进的空格数
set tabstop=2       " Number of spaces that a <Tab> in the file counts for.一个tab用几个空格替换
au FileType html,python,vim,javascript,css setl shiftwidth=2
au FileType html,python,vim,javascript,css setl tabstop=2
au FileType java,php setl shiftwidth=4
au FileType java,php setl tabstop=4

set textwidth=80    " Maximum width of text that is being inserted. A longer
                    " line will be broken after white space to get this width.（每行的最大字符数，超过的话，将换行）
set hlsearch        " When there is a previous search pattern, highlight all
                    " its matches.（搜索时高亮显示）
 
set incsearch       " While typing a search command, show immediately where the
                    " so far typed pattern matches.（搜索时，立即高亮显示输入的字符）
set fileformat=unix   " 文本格式
set nobackup          " 不生成备份文件
" Show a status bar  "  显示状态栏
set ruler
set laststatus=2
" Show Tab Bar
set showtabline=2     " 生成多tab
set tabline+=%f
" Enable Code Folding
set foldenable
set foldmethod=syntax
set mouse=a         " Enable the use of the mouse.（可以使用鼠标）


set cmdheight=1     "设定命令行的行数为 1
"去掉烦死我的错误声音
set vb t_vb=
"工作目录随文件变
autocmd BufEnter * cd %:p:h
"设置状态栏
set statusline=%F%m%r,%Y,%{&fileformat}\ \ \ ASCII=\%b,HEX=\%B\ \ \ %l,%c%V\ %p%%\ \ \ [\ %L\ lines\ in\ all\ ]
"不显示工具条
"set guioptions-=T
set backspace=indent,eol,start "不设定的话在插入状态无法用退格键和 Delete
filetype indent on "设置文件类型的检测，e.g. for PHP
filetype plugin on "为特定的文件类型允许插件文件的载入

" Allow file inline modelines to provide settings
set modeline



"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" => Plugin configuration
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" NERDTree
map <F2> :NERDTreeToggle<CR>
let NERDTreeIgnore=['\.svn$','\.bak$']

" taglist
set tags=tags;/
let Tlist_Ctags_Cmd="/usr/bin/ctags"
map <F3> :TlistToggle<CR>
let Tlist_Auto_Highlight_Tag = 1
let Tlist_Auto_Open = 0
let Tlist_Auto_Update = 1
let Tlist_Close_On_Select = 0
let Tlist_Compact_Format = 0
let Tlist_Display_Prototype = 0
let Tlist_Display_Tag_Scope = 1
let Tlist_Enable_Fold_Column = 0
let Tlist_Exit_OnlyWindow = 0
let Tlist_File_Fold_Auto_Close = 0
let Tlist_GainFocus_On_ToggleOpen = 1
let Tlist_Hightlight_Tag_On_BufEnter = 1
let Tlist_Inc_Winwidth = 0
let Tlist_Max_Submenu_Items = 1
let Tlist_Max_Tag_Length = 30
let Tlist_Process_File_Always = 0
let Tlist_Show_Menu = 0
let Tlist_Show_One_File = 0
let Tlist_Sort_Type = "order"
let Tlist_Use_Horiz_Window = 0
let Tlist_Use_Right_Window = 0
let Tlist_WinWidth = 40
let tlist_php_settings = 'php;c:class;i:interfaces;d:constant;f:function'

" Disable phpsyntax based indenting for .php files {{{
au BufRead,BufNewFile *.php		set indentexpr= | set smartindent
" }}}

" {{{ .phps files handled like .php

au BufRead,BufNewFile *.phps		set filetype=php

" }}}

" {{{  Settings



" MovingThroughCamelCaseWords
nnoremap <silent><C-Left>  :<C-u>cal search('\<\<Bar>\U\@<=\u\<Bar>\u\ze\%(\U\&\>\@!\)\<Bar>\%^','bW')<CR>
nnoremap <silent><C-Right> :<C-u>cal search('\<\<Bar>\U\@<=\u\<Bar>\u\ze\%(\U\&\>\@!\)\<Bar>\%$','W')<CR>
inoremap <silent><C-Left>  <C-o>:cal search('\<\<Bar>\U\@<=\u\<Bar>\u\ze\%(\U\&\>\@!\)\<Bar>\%^','bW')<CR>
inoremap <silent><C-Right> <C-o>:cal search('\<\<Bar>\U\@<=\u\<Bar>\u\ze\%(\U\&\>\@!\)\<Bar>\%$','W')<CR> 

" }}}

" Map <F5> to turn spelling on (VIM 7.0+)
map <F5> :setlocal spell! spelllang=en_us<cr>
" Map <F6> to turn spelling (de) on (VIM 7.0+)
"map <F6> :setlocal spell! spelllang=de<cr>

" Highlight current line in insert mode.
autocmd InsertLeave * se nocul
autocmd InsertEnter * se cul 

if has("cscope")
	set csprg=/usr/bin/cscope
	set csto=0
	set cst
	set nocsverb
	" add any database in current directory
	if filereadable("cscope.out")
	    cs add cscope.out
	" else add database pointed to by environment
	elseif $CSCOPE_DB != ""
	    cs add $CSCOPE_DB
	endif
	set csverb
endif
