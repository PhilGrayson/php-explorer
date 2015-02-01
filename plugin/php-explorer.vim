if exists('g:php_awesome')
  finish
endif
let g:php_awesome = 1
let s:this_path = fnamemodify(resolve(expand('<sfile>:p')), ':h:h')

function! PHPGoTo()
  let l:current_directory = expand('%:p:h')
  let l:line_number       = line(".")
  let l:current_word      = expand('<cWORD>')
  let l:file_contents     = join(getbufline(bufname('%'), 1, '$'), "\n")
  let [l:line_contents]   = getbufline(bufname('%'), l:line_number)

  let l:script_inputs     = [l:current_directory, l:line_number, l:current_word, l:file_contents]

  let l:found             = system(shellescape(s:this_path . '/php/bin/resolve'), join(l:script_inputs, "\n"))
  let l:foundSplit        = split(l:found, "\n")
"echo l:found

  if len(l:foundSplit) == 2
    execute "edit " . l:foundSplit[0]
    execute l:foundSplit[1]
  endif
endfunction

nnoremap <script> <silent> <unique> <Leader>gt :call PHPGoTo()<CR>
