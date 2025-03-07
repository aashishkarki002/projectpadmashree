<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar Component</title>
  <style>
    :root {
      --sidebar-bg:rgb(255, 255, 255);
      --item-hover: #e9ecef;
      --text-color: #333;
      --active-color: #007bff;
      --border-color: #e0e4e8;
      --primary: #0a8a4e;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      height: 100vh;
      display: flex;
    }
    
    .side-bar {
      width: 16rem;
      height: 100vh;
      background-color: var(--sidebar-bg);
      padding: 1.5rem 1rem;
      border-right: 0.0625rem solid var(--border-color);
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
   
    }
    

    
 
    
    .individual {
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }
    
    .individual:hover {
      background-color: var(--item-hover);
    }
    
    .individual.active {
      background-color: rgba(0, 123, 255, 0.1);
      color: var(--active-color);
    }
    
    .icons {
      width: 1.25rem;
      height: 1.25rem;
      margin-right: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .individual div:last-child {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--text-color);
    }
    
    .individual.active div:last-child {
      color: var(--active-color);
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="side-bar">
    <div class="logo-container">
      <div class="logo">
        <img src="" alt="">
      </div>
    </div>
    <div class="individual active" id="home">
      <div class="icons"><i class="fas fa-home"></i></div>
      <div>Home</div>
    </div>
    <div class="individual" id="stats">
      <div class="icons"><i class="fas fa-chart-bar"></i></div>
      <div>Statistics</div>
    </div>
    <div class="individual" id="summary">
      <div class="icons"><i class="fas fa-coins"></i></div>
      <div>Summary</div>
    </div>
    <div class="individual" id="budget">
      <div class="icons"><i class="fas fa-wallet"></i></div>
      <div>Budget</div>
    </div>
    <div class="individual" id="history">
      <div class="icons"><i class="fas fa-history"></i></div>
      <div>History</div>
    </div>
  
  </div>

  <script>
    // Simple navigation script
    document.querySelectorAll('.individual').forEach(item => {
      item.addEventListener('click', function() {
        // Remove active class from all items
        document.querySelectorAll('.individual').forEach(el => {
          el.classList.remove('active');
        });
        // Add active class to clicked item
        this.classList.add('active');
      });
    });
  </script>
</body>
</html>