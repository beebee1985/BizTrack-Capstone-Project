import React, { useState, useEffect } from "react";
import { dashboardService } from "../services";
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from "recharts";

function Dashboard() {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const [dateRange, setDateRange] = useState({
    from: new Date(new Date().getFullYear(), new Date().getMonth(), 1)
      .toISOString()
      .split("T")[0],
    to: new Date().toISOString().split("T")[0],
  });

  useEffect(() => {
    loadStats();
  }, [dateRange]);

  const loadStats = async () => {
    try {
      setLoading(true);
      const response = await dashboardService.getStats(
        dateRange.from,
        dateRange.to,
      );
      setStats(response.data);
    } catch (error) {
      console.error("Failed to load stats:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="loading">
        <h2>Loading dashboard...</h2>
      </div>
    );
  }

  if (!stats) {
    return <div className="error-message">Failed to load dashboard data</div>;
  }

  const { summary, charts } = stats;

  return (
    <div>
      <h1>Dashboard</h1>

      <div className="card mb-20">
        <h3>Date Range</h3>
        <div style={{ display: "flex", gap: "10px" }}>
          <div className="form-group">
            <label>From</label>
            <input
              type="date"
              value={dateRange.from}
              onChange={(e) =>
                setDateRange({ ...dateRange, from: e.target.value })
              }
            />
          </div>
          <div className="form-group">
            <label>To</label>
            <input
              type="date"
              value={dateRange.to}
              onChange={(e) =>
                setDateRange({ ...dateRange, to: e.target.value })
              }
            />
          </div>
        </div>
      </div>

      <div className="stats-grid">
        <div className="stat-card revenue">
          <h4>Total Revenue</h4>
          <div className="value">${summary.total_revenue.toFixed(2)}</div>
        </div>

        <div className="stat-card expense">
          <h4>Total Expenses</h4>
          <div className="value">${summary.total_expenses.toFixed(2)}</div>
        </div>

        <div className="stat-card profit">
          <h4>Profit</h4>
          <div className="value">${summary.profit.toFixed(2)}</div>
        </div>

        <div className="stat-card">
          <h4>Total Sales</h4>
          <div className="value">{summary.total_sales}</div>
        </div>

        <div className="stat-card">
          <h4>Average Sale</h4>
          <div className="value">${summary.average_sale.toFixed(2)}</div>
        </div>

        <div className="stat-card">
          <h4>Customers</h4>
          <div className="value">{summary.total_customers}</div>
        </div>

        <div className="stat-card">
          <h4>Products</h4>
          <div className="value">{summary.total_products}</div>
        </div>
      </div>

      <div className="card mb-20">
        <h3>Sales Over Time</h3>
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={charts.sales_by_date}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Line
              type="monotone"
              dataKey="total"
              stroke="#27ae60"
              name="Sales ($)"
            />
          </LineChart>
        </ResponsiveContainer>
      </div>

      <div className="card mb-20">
        <h3>Expenses by Category</h3>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={charts.expenses_by_category}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="category" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Bar dataKey="total" fill="#e74c3c" name="Amount ($)" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      <div className="card">
        <h3>Top Products</h3>
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Quantity Sold</th>
              <th>Revenue</th>
            </tr>
          </thead>
          <tbody>
            {charts.top_products.map((product, index) => (
              <tr key={index}>
                <td>{product.name}</td>
                <td>{product.total_quantity}</td>
                <td>${parseFloat(product.total_revenue).toFixed(2)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default Dashboard;
