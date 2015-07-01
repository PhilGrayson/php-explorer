if exists('g:phpexplorer_autoloaded') || &cp
    finish
endif
let g:phpexplorer_autoloaded = 1

let s:this_path = fnamemodify(resolve(expand('<sfile>:p')), ':h:h')

function! phpexplorer#GoTo()
    let l:current_directory = expand('%:p:h')
    let l:line_number       = line(".")
    let l:current_word      = expand('<cWORD>')
    let l:file_contents     = join(getbufline(bufname('%'), 1, '$'), "\n")
    let [l:line_contents]   = getbufline(bufname('%'), l:line_number)

    let l:script_inputs     = [l:current_directory, l:line_number, l:current_word, l:file_contents]

    let l:found             = system(shellescape(s:this_path . '/php/bin/resolve'), join(l:script_inputs, "\n"))
    let l:foundSplit        = split(l:found, "\n")

    if l:foundSplit[0] == 'result' && len(l:foundSplit) == 3
        execute "edit " . l:foundSplit[1]
        execute l:foundSplit[2]
    else
        echohl WarningMsg
        echo join(l:foundSplit[1:], ': ')
        echohl none
    endif
endfunction
