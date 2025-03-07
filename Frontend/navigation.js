document.addEventListener('DOMContentLoaded',()=>{

const sidebar = document.querySelectorAll('.individual')

sidebar.forEach(item=>{
    item.addEventListener('click',()=>{
    sidebar.forEach(el => el.classList.remove('active')) 
        item.classList.add('active')
        const pageId = item.id;

        switch (pageId) {
            case 'home':
                window.location.href="home.php"
                break;
                case 'stats':
                    window.location.href="stats.php"
                    break;
                    case 'summary':
                        window.location.href="summary.php"
                        break;
                        case 'budget':
                        window.location.href="budget.php"
                        break;
                        case 'history':
                            window.location.href="history.php"
                            break;
                            case 'saving':
                                window.location.href="saving.php"
                                break;
                            case 'setting':
                                window.location.href="setting.php"
                                break;
                              
    

            default:
             break
        }
        
    });
    });
});


