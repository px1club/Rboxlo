// Rboxlo Matchmaker

const express = require("express")
const mysql = require("mysql2")
const config = require("./config.json")

// Connect to database
var connection = mysql.createPool({
    host: config.database.host,
    port: config.database.port,
    user: config.database.user,
    password: config.database.password,
    dataabse: config.database.name
})

// Init express
var app = express.createServer()
app.use(express.json())

// Utils
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
}

// Matchmaker
app.post("/arrange", async function(req, res) {
    if (isNaN(req.body.id)) { 
        return res.json({"success": false, "reason": "Invalid game ID."})
    }

    let counter = 0

    async function check(id) {
        counter += 1

        if (counter >= 5) {
            return res.json({"success": false, "reason": "Server failed to start."})
        }

        const [job] = await connection.promise().query("SELECT * FROM `jobs` WHERE `game_id` = ?", id)

        if (job.length) {
            return res.json({"success": true, "id": job[0].name})
        } else {
            await sleep(2000)
            check(id)
        }
    }

    check(req.body.id)
})

// Finish
app.listen(config.port)
console.log(`Rboxlo matchmaker listening on port ${config.port}`)