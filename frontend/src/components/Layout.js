import React from "react";
import { NavLink, Outlet, useNavigate } from "react-router-dom";
import { authService } from "../services";

function Layout({ children }) {
  const navigate = useNavigate();
  const user = authService.getCurrentUser();

  const handleLogout = () => {
    authService.logout();
    navigate("/login");
  };

  return (
    <div className="app">
      <aside className="sidebar">
        <h2>BizTrack</h2>
        <div
          style={{
            padding: "0 20px",
            marginBottom: "20px",
            borderBottom: "1px solid #34495e",
            paddingBottom: "10px",
          }}
        >
          <small>Welcome, {user?.full_name}</small>
        </div>
        <nav>
          <ul>
            <li>
              <NavLink to="/dashboard">Dashboard</NavLink>
            </li>
            <li>
              <NavLink to="/products">Products</NavLink>
            </li>
            <li>
              <NavLink to="/customers">Customers</NavLink>
            </li>
            <li>
              <NavLink to="/sales">Sales</NavLink>
            </li>
            <li>
              <NavLink to="/expenses">Expenses</NavLink>
            </li>
            <li>
              <a href="#!" onClick={handleLogout}>
                Logout
              </a>
            </li>
          </ul>
        </nav>
      </aside>
      <main className="main-content"><Outlet /></main>
    </div>
  );
}

export default Layout;
