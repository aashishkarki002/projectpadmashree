<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        :root {
            --sidebar-bg: #f5f7fa;
            --item-hover: #e9ecef;
            --text-color: #333;
            --active-color: #007bff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .side-bar {
            width: 16rem;
            height: 100vh;
            background-color: var(--sidebar-bg);
            padding: 1.5rem 1rem;
            border-right: 0.0625rem solid #e0e4e8;
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
</head>
<body>
    <div class="side-bar">
        <div class="individual active" id="home">
            <div><img src="icons/home.png" alt="" class="icons"></div>
            <div>Home</div>
        </div>
        <div class="individual" id="stats">
            <div><img src="icons/bar-chart-square-01.png" alt="" class="icons"></div>
            <div>Statistics</div>
        </div>
        <div class="individual" id="summary">
            <div><img src="icons/coins-rotate.png" alt="" class="icons"></div>
            <div>Summary</div>
        </div>
        <div class="individual" id="budget">
            <div><img src="icons/budget-icon.png" alt="" class="icons"></div>
            <div>Budget</div>
        </div>
        <div class="individual" id="history">
            <div><img src="icons/history.png" alt="" class="icons"></div>
            <div>History</div>
        </div>
    </div>
</body>
</html>