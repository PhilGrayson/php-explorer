if exists('g:phpexplorer_loaded') || &cp
  finish
endif
let g:phpexplorer_loaded = 1

command! PHPExplorerGoTo call phpexplorer#GoTo()

if !hasmapto(':PHPExplorer<CR>') && maparg('<Leader>gt', 'm') == ''
    silent! nnoremap <script> <silent> <unique> <Leader>gt :PHPExplorerGoTo<CR>
endif
